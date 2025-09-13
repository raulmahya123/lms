<?php

    namespace App\Http\Controllers\User;

    use App\Http\Controllers\Controller;
    use App\Models\{Membership, Plan, Payment};
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Str;
    use Midtrans\Config;
    use Midtrans\Snap;
    use Carbon\Carbon;

    class MembershipController extends Controller
    {
        /**
         * Halaman utama membership user:
         * - Tampilkan membership aktif/terbaru
         * - Riwayat membership user
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
         * Daftar plan yang tersedia untuk dibeli/diupgrade user.
         */
        public function plans()
        {
            // Sesuaikan filter jika ada kolom is_active / is_public di plans
            $plans = Plan::query()
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'period']); // <-- pakai period

            return view('app.memberships.plans', compact('plans'));
        }

        /**
         * Buat membership 'pending' untuk plan tertentu (langkah awal subscribe).
         * Biasanya diarahkan ke halaman pembayaran setelah ini.
         */
        public function subscribe(Request $r, Plan $plan)
        {
            $userId = Auth::id();

            // Reuse pending yang sama jika ada
            $pending = Membership::where('user_id', $userId)
                ->where('plan_id', $plan->id)
                ->where('status', 'pending')
                ->first();

            if ($pending) {
                return redirect()
                    ->route('app.memberships.checkout', $pending)
                    ->with('ok', 'Melanjutkan proses pembayaran membership yang tertunda.');
            }

            // (Opsional) Cegah dobel aktif di plan sama
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

            // Arahkan ke halaman checkout (buat view sederhana untuk instruksi/bayar)
            return redirect()->route('app.memberships.checkout', $membership);
        }

        /**
         * Halaman checkout sederhana (instruksi pembayaran / integrasi gateway).
         */
        public function checkout(Membership $membership)
        {
            $this->ensureOwner($membership);

            // Pastikan status masih pending
            if ($membership->status !== 'pending') {
                return redirect()->route('app.memberships.index')
                    ->with('info', 'Membership ini tidak dalam status pending.');
            }

            // muat plan dengan period
            $membership->load('plan:id,name,price,period');

            return view('app.memberships.checkout', compact('membership'));
        }

        /**
         * Endpoint aktivasi membership setelah pembayaran sukses.
         * Untuk keamanan, tambahkan verifikasi signature/payment status sesuai gateway Anda.
         */
        public function activate(Request $r, Membership $membership)
        {
            $this->ensureOwner($membership);

            if ($membership->status === 'active') {
                return back()->with('ok', 'Membership sudah aktif.');
            }

            // validasi sederhana; di real case verifikasi payment_id / reference
            $r->validate([
                'reference' => ['nullable', 'string', 'max:190'],
            ]);

            $plan = $membership->plan()->first(['id', 'period']);

            // Hitung expiry berdasarkan period
            $now = Carbon::now();
            $expiresAt = match ($plan?->period) {
                'yearly'  => $now->copy()->addYear(),
                'monthly' => $now->copy()->addMonth(),
                default   => $now->copy()->addMonth(), // fallback
            };

            DB::transaction(function () use ($membership, $now, $expiresAt, $r) {
                $activatedAt = $membership->activated_at ?: $now;

                $membership->update([
                    'status'       => 'active',
                    'activated_at' => $activatedAt,
                    'expires_at'   => $expiresAt,
                    // 'payment_reference' => $r->reference ?? null, // jika punya kolom ini
                ]);
            });

            return redirect()->route('app.memberships.index')->with('ok', 'Membership berhasil diaktifkan.');
        }

        /**
         * Nonaktifkan / batalkan membership user.
         * - Set status ke 'inactive'
         * - Atur expires_at = now (atau biarkan berjalan hingga habis jika mau)
         */
        public function cancel(Membership $membership)
        {
            $this->ensureOwner($membership);

            if ($membership->status !== 'active' && $membership->status !== 'pending') {
                return back()->with('info', 'Membership sudah tidak aktif.');
            }

            $membership->update([
                'status'     => 'inactive',
                'expires_at' => Carbon::now(),
            ]);

            return back()->with('ok', 'Membership dibatalkan.');
        }

        /**
         * Update status membership oleh user (opsional; biasanya admin yang ubah)
         * Disediakan untuk kasus tertentu, mis. downgrade manual sebelum habis masa berlaku.
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


        public function startSnap(Request $r, Membership $membership)
        {   
            $this->ensureOwner($membership);
            abort_if($membership->status !== 'pending', 400, 'Membership bukan pending');

            $plan   = $membership->plan()->first(['id','name','price','period']);
            $amount = (int) ($plan->price ?? 0);

            //   harga 0 -> aktifkan tanpa Midtrans
            if ($amount <= 0) {
                $now = now();
                $expires = $plan?->period === 'yearly' ? $now->clone()->addYear() : $now->clone()->addMonth();
                $membership->update(['status'=>'active','activated_at'=>$now,'expires_at'=>$expires]);
                return response()->json(['free' => true]);
            }

            // buat/ambil payment pending
            $payment = \App\Models\Payment::firstOrCreate(
                [
                    'user_id'       => auth()->id(),
                    'plan_id' => $plan->id,
                    'status'        => 'pending',
                    'provider'      => 'midtrans',
                ],
                [
                    'amount'    => $amount,
                    // order_id jangan terlalu panjang & harus unik
                    'reference' => 'MBR-'.time().'-'.auth()->id(),
                ]
            );

            // jika sebelumnya sudah punya token, JANGAN buat ulang
            if ($payment->snap_token) {
            return response()->json(['snap_token' => $payment->snap_token]);
        }

            // Midtrans config
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

    // Validasi minimal: gross_amount harus >= 100 di sandbox
        if ($payment->amount < 100) {
        return response()->json([
            'message' => 'Nominal terlalu kecil (min 100 di sandbox).'
        ], 422);
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
            'name'     => $plan->name,
        ]],
    ];

    try {
        $trx = \Midtrans\Snap::createTransaction($params);
        $payment->snap_token        = $trx->token ?? null;
        $payment->snap_redirect_url = $trx->redirect_url ?? null;
        $payment->save();

        if (!$payment->snap_token) {
            \Log::error('Midtrans: token null', ['response' => $trx ?? null]);
            return response()->json(['message'=>'Midtrans tidak memberi token'], 422);
        }

        return response()->json(['snap_token' => $payment->snap_token]);
    } catch (\Throwable $e) {
        \Log::error('Midtrans createTransaction failed', [
            'error'   => $e->getMessage(),
            'orderId' => $payment->reference,
            'amount'  => $payment->amount,
        ]);
        return response()->json(['message'=>'Midtrans error: '.$e->getMessage()], 422);
    }
}
        /**
         * Helper: pastikan membership milik user login.
         */
        private function ensureOwner(Membership $membership): void
        {
            if ($membership->user_id !== Auth::id()) {
                abort(403);
            }
        }
    }