<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Payment, Enrollment, Membership, Coupon, CouponRedemption};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Midtrans SDK
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap as MidtransSnap;
use Midtrans\Transaction as MidtransTransaction;

class CourseCheckoutController extends Controller
{
    /* ============================
     * Helpers: Membership & Access
     * ============================ */

    /** Cek membership aktif (support UUID string). */
    private function userHasActiveMembership(int|string|null $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return false;

        return Membership::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /** Ambil membership aktif untuk copy expires_at. */
    private function getActiveMembership(int|string|null $userId = null): ?Membership
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return null;

        return Membership::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('expires_at')
            ->first();
    }

    /** Akses efektif enrollment (memperhitungkan membership sekarang). */
    private function hasEffectiveAccess(?Enrollment $enr, bool $hasMembership): bool
    {
        if (!$enr || $enr->status !== 'active') return false;

        // Purchase / Free = selalu efektif
        if (in_array($enr->access_via, ['purchase','free'], true)) return true;

        // Membership = efektif hanya jika membership saat ini aktif & belum lewat expiry di enrollment
        if ($enr->access_via === 'membership') {
            $notExpired = is_null($enr->access_expires_at) || now()->lt($enr->access_expires_at);
            return $hasMembership && $notExpired;
        }

        // Fallback lama (enrollment lama tanpa access_via)
        if (empty($enr->access_via)) return true;

        return false;
    }

    /* ==============
     * Checkout Page
     * ============== */

    public function checkout(Course $course)
    {
        abort_unless($course->is_published, 404);

        $enr = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        $hasMembership      = $this->userHasActiveMembership(Auth::id());
        $hasEffectiveAccess = $this->hasEffectiveAccess($enr, $hasMembership);

        // Masih punya akses efektif → tidak perlu checkout
        if ($hasEffectiveAccess) {
            return redirect()->route('app.my.courses')
                ->with('info', 'Kamu sudah ter-enroll dan masih punya akses ke course ini.');
        }

        // Akses tidak efektif → tetap tampilkan checkout
        $clientKey = config('services.midtrans.client_key');
        $isSandbox = !config('services.midtrans.is_production');

        return view('app.courses.checkout', compact('course', 'clientKey', 'isSandbox', 'hasMembership'));
    }

    /* ===========================
     * Start Snap / Apply Coupon
     * =========================== */

    public function startSnap(Request $r, Course $course)
    {
        abort_unless($course->is_published, 404);

        $enr = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        $hasMembership      = $this->userHasActiveMembership(Auth::id());
        $hasEffectiveAccess = $this->hasEffectiveAccess($enr, $hasMembership);

        // Sudah punya akses efektif? stop.
        if ($hasEffectiveAccess) {
            return response()->json(['message' => 'Kamu sudah ter-enroll dan masih punya akses ke course ini.'], 409);
        }

        // Membership aktif → enroll gratis mengikuti masa membership
        if ($hasMembership) {
            $m   = $this->getActiveMembership(Auth::id());
            $exp = $m?->expires_at;

            $enr = Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                [
                    'status'            => 'active',
                    'activated_at'      => now(),
                    'access_via'        => 'membership',
                    'access_expires_at' => $exp,
                ]
            );

            if (!$enr->wasRecentlyCreated) {
                $enr->update([
                    'status'            => 'active',
                    'access_via'        => 'membership',
                    'access_expires_at' => $exp,
                ]);
            }

            return response()->json(['free' => true, 'membership' => true]);
        }

        // Sudah pernah bayar sukses?
        $paidExists = Payment::where([
            'user_id'   => Auth::id(),
            'course_id' => $course->id,
            'provider'  => 'midtrans',
            'status'    => 'paid',
        ])->exists();
        if ($paidExists) {
            return response()->json(['message' => 'Pembayaran course ini sudah berhasil.'], 409);
        }

        /* ---------- Pricing + Coupon ---------- */
        $baseAmount = (int) ($course->price ?? 0);
        $couponId   = $r->input('coupon_id');
        $couponCode = $r->input('coupon_code');

        $appliedCoupon = null;
        $discount      = 0;

        if ($couponId || $couponCode) {
            $appliedCoupon = $couponId
                ? Coupon::find($couponId)
                : Coupon::whereRaw('LOWER(code)=?', [strtolower((string)$couponCode)])->first();

            // Validasi kupon server-side
            if ($appliedCoupon) {
                $now = now();
                if ($appliedCoupon->valid_from && $appliedCoupon->valid_from->gt($now)) $appliedCoupon = null;
                if ($appliedCoupon && $appliedCoupon->valid_until && $appliedCoupon->valid_until->lt($now)) $appliedCoupon = null;
                if ($appliedCoupon && $appliedCoupon->usage_limit && $appliedCoupon->redemptions()->count() >= $appliedCoupon->usage_limit) $appliedCoupon = null;

                if ($appliedCoupon) {
                    $alreadyUse = CouponRedemption::where('coupon_id', $appliedCoupon->id)
                        ->where('user_id', Auth::id())
                        ->where('course_id', $course->id)
                        ->exists();
                    if ($alreadyUse) $appliedCoupon = null;
                }

                if ($appliedCoupon) {
                    $discount = (int) round($baseAmount * ($appliedCoupon->discount_percent / 100));
                }
            }
        }

        $finalAmount = max(0, $baseAmount - $discount);

        // Full discount (kupon 100% atau harga 0) → enroll gratis + catat redemption bila ada
        if ($finalAmount <= 0) {
            Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                [
                    'status'            => 'active',
                    'activated_at'      => now(),
                    'access_via'        => 'free',
                    'access_expires_at' => null,
                ]
            );

            if ($appliedCoupon) {
                CouponRedemption::firstOrCreate(
                    [
                        'coupon_id' => $appliedCoupon->id,
                        'user_id'   => Auth::id(),
                        'course_id' => $course->id,
                    ],
                    [
                        'used_at'           => now(),
                        'amount_discounted' => $discount,
                    ]
                );
            }

            return response()->json(['free' => true]);
        }

        /* ---------- Buat/Reuse Payment ---------- */
        $newReference = 'CRS-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(6));

        $payment = Payment::firstOrCreate(
            [
                'user_id'   => Auth::id(),
                'course_id' => $course->id,
                'status'    => 'pending',
                'provider'  => 'midtrans',
            ],
            [
                'amount'    => $finalAmount,
                'reference' => $newReference,
            ]
        );

        // Sinkronkan amount/kupon jika user gonta-ganti kupon
        $payment->amount          = $finalAmount;
        $payment->discount_amount = $discount;
        $payment->coupon_id       = $appliedCoupon?->id;

        if (empty($payment->reference) || strlen($payment->reference) > 50) {
            $payment->reference = $newReference;
        }
        $payment->save();

        if ($payment->snap_token) {
            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id'   => $payment->reference,
            ]);
        }

        // Midtrans config
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        if ($payment->amount < 100) {
            return response()->json(['message' => 'Nominal minimal 100 (sandbox).'], 422);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $payment->reference,
                'gross_amount' => $payment->amount, // harga akhir
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'item_details' => [[
                'id'       => (string) $course->id,
                'price'    => $payment->amount,
                'quantity' => 1,
                'name'     => mb_strimwidth($course->title, 0, 50, ''),
            ]],
        ];

        try {
            $trx = MidtransSnap::createTransaction($params);
            $payment->snap_token        = $trx->token ?? null;
            $payment->snap_redirect_url = $trx->redirect_url ?? null;
            $payment->save();

            if (!$payment->snap_token) {
                Log::error('Midtrans: token null (course)', ['response' => $trx ?? null]);
                return response()->json(['message' => 'Midtrans tidak memberi token'], 422);
            }

            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id'   => $payment->reference,
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans createTransaction failed (course)', [
                'error'   => $e->getMessage(),
                'orderId' => $payment->reference,
            ]);
            return response()->json(['message' => 'Midtrans error: ' . $e->getMessage()], 422);
        }
    }

    /* =================
     * Finish (fallback)
     * ================= */

    public function finish(Request $r)
    {
        $orderId = (string) $r->query('order_id');
        if (!$orderId) {
            return redirect()->route('app.my.courses')->with('info', 'Kembali ke My Courses.');
        }

        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        try {
            $statusResp = MidtransTransaction::status($orderId);
            $transactionStatus = $statusResp->transaction_status ?? null; // settlement|capture|pending|expire|deny|cancel|failure
            $fraudStatus       = $statusResp->fraud_status ?? null;

            $payment = Payment::where('reference', $orderId)->first();
            if ($payment && $payment->status !== 'paid') {
                if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                    $this->markPaidAndEnroll($payment);
                } elseif ($transactionStatus === 'pending') {
                    $payment->update(['status' => 'pending']);
                } else {
                    $payment->update(['status' => 'failed']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Finish status check failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
        }

        return redirect()->route('app.my.courses');
    }

    /** Tandai paid + aktifkan enrollment (akses permanen purchase) + catat redemption jika ada. */
    private function markPaidAndEnroll(Payment $payment): void
    {
        if ($payment->status === 'paid') return;

        $payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        // Catat kupon kalau ada
        if (!empty($payment->coupon_id) && (int) $payment->discount_amount > 0) {
            CouponRedemption::firstOrCreate(
                [
                    'coupon_id' => $payment->coupon_id,
                    'user_id'   => $payment->user_id,
                    'course_id' => $payment->course_id,
                ],
                [
                    'used_at'           => now(),
                    'amount_discounted' => $payment->discount_amount,
                ]
            );
        }

        Enrollment::updateOrCreate(
            ['user_id' => $payment->user_id, 'course_id' => $payment->course_id],
            [
                'status'            => 'active',
                'activated_at'      => now(),
                'access_via'        => 'purchase',
                'access_expires_at' => null,
            ]
        );

        Log::info('Enrollment paid->active (purchase)', [
            'payment_id' => $payment->id,
            'user_id'    => $payment->user_id,
            'course_id'  => $payment->course_id,
        ]);
    }

    /* =======================
     * Enroll Gratis (optional)
     * ======================= */

    public function enroll(Request $r, Course $course)
    {
        abort_unless($course->is_published, 404);

        $enr = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        $hasMembership      = $this->userHasActiveMembership(Auth::id());
        $hasEffectiveAccess = $this->hasEffectiveAccess($enr, $hasMembership);
        if ($hasEffectiveAccess) {
            return redirect()->route('app.my.courses')->with('ok', 'Kamu sudah ter-enroll.');
        }

        $isFreeCourse = ((int) ($course->price ?? 0)) <= 0;

        if ($hasMembership || $isFreeCourse) {
            $m   = $hasMembership ? $this->getActiveMembership(Auth::id()) : null;
            $via = $hasMembership ? 'membership' : 'free';
            $exp = $hasMembership ? ($m?->expires_at) : null;

            $enr = Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                [
                    'status'            => 'active',
                    'activated_at'      => now(),
                    'access_via'        => $via,
                    'access_expires_at' => $exp,
                ]
            );

            if (!$enr->wasRecentlyCreated) {
                $enr->update([
                    'status'            => 'active',
                    'access_via'        => $via,
                    'access_expires_at' => $exp,
                ]);
            }

            return redirect()->route('app.my.courses')->with('ok', 'Berhasil enroll.');
        }

        return redirect()->route('app.courses.checkout', $course)
            ->with('info', 'Silakan lanjutkan pembayaran untuk course ini.');
    }
}
