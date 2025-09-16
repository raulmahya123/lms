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
use Midtrans\Config;
use Midtrans\Snap;

class MembershipController extends Controller
{
    /**
     * Halaman utama membership user
     */
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

    /**
     * Daftar plan tersedia
     */
    public function plans()
    {
        $plans = Plan::query()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'period']);

        return view('app.memberships.plans', compact('plans'));
    }

    /**
     * Buat membership pending lalu arahkan ke checkout
     */
    public function subscribe(Request $r, Plan $plan)
    {
        $userId = Auth::id();

        $pending = Membership::where('user_id', $userId)
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()
                ->route('app.memberships.checkout', $pending)
                ->with('ok', 'Melanjutkan pembayaran membership yang tertunda.');
        }

        $alreadyActive = Membership::where('user_id', $userId)
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->exists();

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

    /**
     * Halaman checkout
     */
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

    /**
     * Aktivasi manual (untuk dev)
     */
    public function activate(Request $r, Membership $membership)
    {
        $this->ensureOwner($membership);

        if ($membership->status === 'active') {
            return back()->with('ok', 'Membership sudah aktif.');
        }

        $r->validate([
            'reference' => ['nullable', 'string', 'max:190'],
        ]);

        $plan = $membership->plan()->first(['id', 'period']);

        $now = Carbon::now();
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

    /**
     * Batalkan/nonaktifkan membership
     */
    public function cancel(Membership $membership)
    {
        $this->ensureOwner($membership);

        if (!in_array($membership->status, ['active', 'pending'], true)) {
            return back()->with('info', 'Membership sudah tidak aktif.');
        }

        $membership->update([
            'status'     => 'inactive',
            'expires_at' => Carbon::now(),
        ]);

        return back()->with('ok', 'Membership dibatalkan.');
    }

    /**
     * Update status membership (opsional)
     */
    public function update(Request $r, Membership $membership)
    {
        $this->ensureOwner($membership);

        $data = $r->validate([
            'status'       => ['required', Rule::in(['pending', 'active', 'inactive'])],
            'activated_at' => ['nullable', 'date'],
            'expires_at'   => ['nullable', 'date', 'after:activated_at'],
        ]);

        $membership->update($data);

        return back()->with('ok', 'Membership diperbarui.');
    }

    /**
     * Buat Snap Token (relies on webhook untuk aktivasi)
     */
    public function startSnap(Request $r, Membership $membership)
    {
        $this->ensureOwner($membership);
        abort_if($membership->status !== 'pending', 400, 'Membership bukan pending');

        $plan   = $membership->plan()->first(['id', 'name', 'price', 'period']);
        $amount = (int) ($plan->price ?? 0);

        if ($amount <= 0) {
            $now = now();
            $expires = $plan?->period === 'yearly'
                ? $now->clone()->addYear()
                : $now->clone()->addMonth();

            $membership->update([
                'status'       => 'active',
                'activated_at' => $now,
                'expires_at'   => $expires,
            ]);

            return response()->json(['free' => true]);
        }

        // === Buat/ambil payment pending yang terikat ke membership ===
        $payment = \App\Models\Payment::firstOrCreate(
            [
                'user_id'       => Auth::id(),
                'membership_id' => $membership->id,
                'plan_id'       => $plan->id,
                'status'        => 'pending',
                'provider'      => 'midtrans',
            ],
            [
                'amount'    => $amount,
                'reference' => $this->makeOrderId($membership), // <= order_id pendek
            ]
        );

        // Jika reference lama kepanjangan / kosong, regenerasi
        if (! $payment->reference || strlen($payment->reference) > 50) {
            $payment->reference         = $this->makeOrderId($membership);
            $payment->snap_token        = null; // reset jaga2
            $payment->snap_redirect_url = null;
            $payment->save();
        }

        if ($payment->snap_token) {
            return response()->json([
                'snap_token'  => $payment->snap_token,
                'redirect_url' => $payment->snap_redirect_url,
            ]);
        }

        // Midtrans config
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        if ($payment->amount < 100) {
            return response()->json(['message' => 'Nominal terlalu kecil (min 100 di sandbox).'], 422);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $payment->reference,
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'item_details' => [[
                'id'       => (string) $plan->id,
                'price'    => $payment->amount,
                'quantity' => 1,
                'name'     => $plan->name,
            ]],
        ];

        try {
            $trx = \Midtrans\Snap::createTransaction($params);
            $payment->snap_token        = $trx->token ?? null;
            $payment->snap_redirect_url = $trx->redirect_url ?? null;
            $payment->save();

            if (!$payment->snap_token) {
                Log::error('Midtrans: token null', ['response' => $trx ?? null]);
                return response()->json(['message' => 'Midtrans tidak memberi token'], 422);
            }

            return response()->json([
                'snap_token'  => $payment->snap_token,
                'redirect_url' => $payment->snap_redirect_url,
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans createTransaction failed', [
                'error'   => $e->getMessage(),
                'orderId' => $payment->reference,
                'amount'  => $payment->amount,
            ]);
            return response()->json(['message' => 'Midtrans error: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Generator order_id pendek (< 50 char, aman untuk Midtrans)
     */
    private function makeOrderId(Membership $membership): string
    {
        // MBR-YYMMDDHHIISS-XXXXXXXX-ABCD -> ±30 char
        return 'MBR-'
            . now()->format('ymdHis') . '-'
            . substr((string) $membership->id, 0, 8) . '-'
            . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(4));
    }


    /**
     * Pastikan membership milik user login
     */
    private function ensureOwner(Membership $membership): void
    {
        if ($membership->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
