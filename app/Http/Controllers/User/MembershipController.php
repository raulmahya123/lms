<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Membership, Plan, Payment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Midtrans SDK
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap as MidtransSnap;
use Midtrans\Transaction as MidtransTransaction;

class MembershipController extends Controller
{
    /** Halaman utama membership user */
    public function index(Request $r)
    {
        $user = Auth::user();

        $current = Membership::with(['plan:id,name,period,price'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        $history = Membership::with(['plan:id,name,period,price'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('app.memberships.index', compact('current', 'history'));
    }

    /** Daftar plan tersedia */
    public function plans()
    {
        $plans = Plan::query()->orderBy('name')->get(['id','name','price','period']);
        return view('app.memberships.plans', compact('plans'));
    }

    /** Buat membership pending lalu arahkan ke checkout */
    public function subscribe(Request $r, Plan $plan)
    {
        $userId = Auth::id();

        $pending = Membership::where('user_id', $userId)
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')->first();

        if ($pending) {
            return redirect()->route('app.memberships.checkout', $pending)
                ->with('ok', 'Melanjutkan pembayaran membership yang tertunda.');
        }

        $alreadyActive = Membership::where('user_id', $userId)
            ->where('plan_id', $plan->id)
            ->where('status', 'active')->exists();

        if ($alreadyActive) {
            return back()->with('info', 'Anda sudah memiliki membership aktif pada plan ini.');
        }

        $membership = Membership::create([
            'user_id'      => $userId,
            'plan_id'      => $plan->id,
            'status'       => 'pending',
            'activated_at' => null,
            'expires_at'   => null,
        ]);

        return redirect()->route('app.memberships.checkout', $membership);
    }

    /** Halaman checkout */
    public function checkout(Membership $membership)
    {
        $this->ensureOwner($membership);

        if ($membership->status !== 'pending') {
            return redirect()->route('app.memberships.index')
                ->with('info', 'Membership ini tidak dalam status pending.');
        }

        $membership->load('plan:id,name,price,period');
        return view('app.memberships.checkout', compact('membership'));
    }

    /** Aktivasi manual (untuk dev) */
    public function activate(Request $r, Membership $membership)
    {
        $this->ensureOwner($membership);

        if ($membership->status === 'active') {
            return back()->with('ok', 'Membership sudah aktif.');
        }

        $plan = $membership->plan()->first(['id','period']);
        $now  = now();
        $expiresAt = match ($plan?->period) {
            'yearly'  => $now->copy()->addYear(),
            'monthly' => $now->copy()->addMonth(),
            default   => $now->copy()->addMonth(),
        };

        DB::transaction(function () use ($membership, $now, $expiresAt) {
            $activatedAt = $membership->activated_at ?: $now;

            $membership->update([
                'status'       => 'active',
                'activated_at' => $activatedAt,
                'expires_at'   => $expiresAt,
            ]);
        });

        return redirect()->route('app.memberships.index')->with('ok', 'Membership berhasil diaktifkan.');
    }

    /** Batalkan/nonaktifkan membership */
    public function cancel(Membership $membership)
    {
        $this->ensureOwner($membership);

        if (!in_array($membership->status, ['active','pending'], true)) {
            return back()->with('info', 'Membership sudah tidak aktif.');
        }

        $membership->update([
            'status'     => 'inactive',
            'expires_at' => now(),
        ]);

        return back()->with('ok', 'Membership dibatalkan.');
    }

    /** Update status membership (opsional) */
    public function update(Request $r, Membership $membership)
    {
        $this->ensureOwner($membership);

        $data = $r->validate([
            'status'       => ['required', Rule::in(['pending','active','inactive'])],
            'activated_at' => ['nullable','date'],
            'expires_at'   => ['nullable','date','after:activated_at'],
        ]);

        $membership->update($data);
        return back()->with('ok', 'Membership diperbarui.');
    }

    /**
     * Buat Snap Token (tanpa webhook; finalisasi pakai /memberships/finish?order_id=...)
     */
    public function startSnap(Request $r, Membership $membership)
    {
        $this->ensureOwner($membership);
        abort_if($membership->status !== 'pending', 400, 'Membership bukan pending');

        $plan   = $membership->plan()->first(['id','name','price','period']);
        $amount = (int) ($plan->price ?? 0);

        // Gratis → langsung aktif
        if ($amount <= 0) {
            $now = now();
            $expires = $plan?->period === 'yearly' ? $now->clone()->addYear() : $now->clone()->addMonth();

            $membership->update([
                'status'       => 'active',
                'activated_at' => $now,
                'expires_at'   => $expires,
            ]);

            return response()->json(['free' => true]);
        }

        // Payment pending (reusable)
        $payment = Payment::firstOrCreate(
            [
                'user_id'       => auth()->id(),
                'membership_id' => $membership->id,
                'plan_id'       => $plan->id,
                'status'        => 'pending',
                'provider'      => 'midtrans',
            ],
            [
                'amount'    => $amount,
                'reference' => $this->makeOrderId($membership), // order_id < 50
            ]
        );

        // Pastikan reference aman
        if (!$payment->reference || strlen($payment->reference) > 50) {
            $payment->reference         = $this->makeOrderId($membership);
            $payment->snap_token        = null;
            $payment->snap_redirect_url = null;
            $payment->save();
        }

        // Reuse token jika masih ada
        if ($payment->snap_token) {
            return response()->json([
                'snap_token'   => $payment->snap_token,
                'redirect_url' => $payment->snap_redirect_url,
                'order_id'     => $payment->reference, // PENTING untuk /finish
            ]);
        }

        // Midtrans config
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        if ($payment->amount < 100) {
            return response()->json(['message' => 'Nominal terlalu kecil (min 100 di sandbox).'], 422);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $payment->reference,
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email'      => auth()->user()->email,
            ],
            'item_details' => [[
                'id'       => (string) $plan->id,
                'price'    => $payment->amount,
                'quantity' => 1,
                'name'     => mb_strimwidth($plan->name, 0, 50, ''),
            ]],
        ];

        try {
            $trx = MidtransSnap::createTransaction($params);
            $payment->snap_token        = $trx->token ?? null;
            $payment->snap_redirect_url = $trx->redirect_url ?? null;
            $payment->save();

            if (!$payment->snap_token) {
                Log::error('Midtrans: token null (membership)', ['response' => $trx ?? null]);
                return response()->json(['message' => 'Midtrans tidak memberi token'], 422);
            }

            return response()->json([
                'snap_token'   => $payment->snap_token,
                'redirect_url' => $payment->snap_redirect_url,
                'order_id'     => $payment->reference, // ← dikirim ke frontend
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans createTransaction failed (membership)', [
                'error'   => $e->getMessage(),
                'orderId' => $payment->reference,
                'amount'  => $payment->amount,
            ]);
            return response()->json(['message' => 'Midtrans error: ' . $e->getMessage()], 422);
        }
    }

    /**
     * FINISH page: fallback tanpa webhook
     * - Ambil status dari Midtrans berdasarkan order_id (payments.reference)
     * - Update payments + aktifkan membership jika paid
     */
    public function finish(Request $r)
    {
        $orderId = (string) $r->query('order_id', '');

        if (!$orderId) {
            return redirect()->route('app.memberships.index')->with('info', 'Kembali ke Membership.');
        }

        // Cari payment berdasar reference
        $payment = Payment::where('reference', $orderId)->first();
        if (!$payment) {
            return redirect()->route('app.memberships.index')->with('info', 'Transaksi tidak ditemukan.');
        }

        // Lindungi akses user
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        // Midtrans config
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        try {
            // Ambil status transaksi dari Midtrans
            $statusResp = MidtransTransaction::status($orderId);

            $trxStatus = (string) ($statusResp->transaction_status ?? ''); // settlement|capture|pending|expire|deny|cancel|failure
            $fraud     = (string) ($statusResp->fraud_status ?? '');
            $gross     = (string) ($statusResp->gross_amount ?? '');
            $ptype     = (string) ($statusResp->payment_type ?? '');
            $trxId     = (string) ($statusResp->transaction_id ?? '');
            $settledAt = (string) ($statusResp->settlement_time ?? '');

            // Map status Midtrans -> lokal
            $newStatus = match ($trxStatus) {
                'capture'    => ($fraud === 'challenge') ? 'pending' : 'paid',
                'settlement' => 'paid',
                'pending'    => 'pending',
                'cancel', 'deny', 'expire', 'failure' => 'failed',
                default      => 'pending',
            };

            DB::transaction(function () use ($payment, $newStatus, $gross, $ptype, $trxId, $settledAt) {
                // Update payment
                $payment->provider = 'midtrans';
                $payment->status   = $newStatus;

                if (is_numeric($gross)) {
                    $payment->amount = (int) $gross;
                }
                if ($trxId && $this->schemaHas('payments','midtrans_transaction_id')) {
                    $payment->midtrans_transaction_id = $trxId;
                }
                if ($ptype && $this->schemaHas('payments','midtrans_payment_type')) {
                    $payment->midtrans_payment_type = $ptype;
                }
                if ($settledAt && $this->schemaHas('payments','midtrans_settlement_time')) {
                    try { $payment->midtrans_settlement_time = Carbon::parse($settledAt); } catch (\Throwable $e) {}
                }

                if ($newStatus === 'paid') {
                    $payment->paid_at = now();
                }
                $payment->save();

                // Aktifkan membership jika paid
                if ($newStatus === 'paid' && $payment->membership_id) {
                    $membership = Membership::find($payment->membership_id);
                    $plan       = $payment->plan_id ? Plan::find($payment->plan_id) : null;

                    if ($membership && $plan) {
                        $now = now();

                        // Jika sudah aktif & belum habis, extend dari expires_at; kalau tidak, start dari now
                        $from = ($membership->status === 'active' && $membership->expires_at && $membership->expires_at->isFuture())
                            ? $membership->expires_at
                            : $now;

                        $expires = ($plan->period === 'yearly') ? $from->copy()->addYear() : $from->copy()->addMonth();

                        $membership->update([
                            'status'       => 'active',
                            'activated_at' => $membership->activated_at ?: $now,
                            'expires_at'   => $expires,
                        ]);
                    }
                }
            });

        } catch (\Throwable $e) {
            Log::error('Membership finish() status check failed', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);
            // Jangan blokir user; tetap arahkan ke index
        }

        return redirect()->route('app.memberships.index');
    }

    /** Generator order_id pendek (< 50 char, aman untuk Midtrans) */
    private function makeOrderId(Membership $membership): string
    {
        // MBR-YYMMDDHHIISS-XXXXXXXX-ABCD → ±30 char
        return 'MBR-'
            . now()->format('ymdHis') . '-'
            . substr((string) $membership->id, 0, 8) . '-'
            . Str::upper(Str::random(4));
    }

    /** Pastikan membership milik user login */
    private function ensureOwner(Membership $membership): void
    {
        if ($membership->user_id !== Auth::id()) abort(403);
    }

    /** Helper cek kolom ada */
    private function schemaHas(string $table, string $column): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    }
}
