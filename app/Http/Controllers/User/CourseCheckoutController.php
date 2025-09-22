<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Payment, Enrollment, Coupon, CouponRedemption};
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
    public function checkout(Course $course)
    {
        // Jika sudah enroll, langsung balik
        $already = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if ($already) {
            return redirect()->route('app.my.courses')
                ->with('info', 'Kamu sudah ter-enroll di course ini.');
        }

        $clientKey = config('services.midtrans.client_key');
        $isSandbox = !config('services.midtrans.is_production');

        return view('app.courses.checkout', compact('course', 'clientKey', 'isSandbox'));
    }

    public function startSnap(Request $r, Course $course)
    {
        abort_unless($course->is_published, 404);

        // Guard: sudah aktif?
        $already = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')->exists();
        if ($already) return response()->json(['message' => 'Kamu sudah ter-enroll di course ini.'], 409);

        // Guard: sudah ada payment paid?
        $paidExists = Payment::where([
            'user_id'   => Auth::id(),
            'course_id' => $course->id,
            'provider'  => 'midtrans',
            'status'    => 'paid',
        ])->exists();
        if ($paidExists) return response()->json(['message' => 'Pembayaran course ini sudah berhasil.'], 409);

        $baseAmount = (int) ($course->price ?? 0);
        $couponId   = $r->input('coupon_id');
        $couponCode = $r->input('coupon_code');

        // Re-validate coupon di server
        $appliedCoupon = null;
        $discount      = 0;
        if ($couponId || $couponCode) {
            $appliedCoupon = $couponId
                ? Coupon::find($couponId)
                : Coupon::whereRaw('LOWER(code)=?', [strtolower($couponCode)])->first();

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

        // Full discount → langsung enroll + catat redemption
        if ($finalAmount <= 0) {
            Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                ['status' => 'active', 'activated_at' => now()]
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

        // Payment pending
        $newReference = 'CRS-' . now()->format('ymdHis') . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));

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

        // Sinkronisasi nilai (kalau user apply/remove kupon berkali-kali)
        $payment->amount          = $finalAmount;
        $payment->discount_amount = $discount;
        $payment->coupon_id       = $appliedCoupon?->id;
        if (empty($payment->reference) || strlen($payment->reference) > 50) {
            $payment->reference = $newReference;
        }
        $payment->save();

        // Reuse snap_token bila ada
        if ($payment->snap_token) {
            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id'   => $payment->reference,
            ]);
        }

        // Midtrans config
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        if ($payment->amount < 100) {
            return response()->json(['message' => 'Nominal minimal 100 (sandbox).'], 422);
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
                'id'       => (string) $course->id,
                'price'    => $payment->amount, // harga final
                'quantity' => 1,
                'name'     => mb_strimwidth($course->title, 0, 50, ''),
            ]],
        ];

        try {
            $trx = \Midtrans\Snap::createTransaction($params);
            $payment->snap_token        = $trx->token ?? null;
            $payment->snap_redirect_url = $trx->redirect_url ?? null;
            $payment->save();

            if (!$payment->snap_token) {
                \Log::error('Midtrans: token null (course)', ['response' => $trx ?? null]);
                return response()->json(['message' => 'Midtrans tidak memberi token'], 422);
            }

            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id'   => $payment->reference,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Midtrans createTransaction failed (course)', [
                'error'   => $e->getMessage(),
                'orderId' => $payment->reference,
            ]);
            return response()->json(['message' => 'Midtrans error: ' . $e->getMessage()], 422);
        }
    }



    /**
     * Fallback lokal: cek status ke Midtrans berdasarkan order_id,
     * lalu aktifkan enrollment jika sudah settlement/capture-accept.
     */
    public function finish(Request $r)
    {
        $orderId = $r->query('order_id');

        // Tanpa order_id → langsung ke My Courses
        if (!$orderId) {
            return redirect()->route('app.my.courses')->with('info', 'Kembali ke My Courses.');
        }

        // Midtrans config (sandbox di lokal)
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        try {
            // Ambil status transaksi dari Midtrans
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
                    // expire / cancel / deny / failure / capture fraud!=accept
                    $payment->update(['status' => 'failed']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Finish status check failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            // Tetap lanjut, jangan blokir user
        }

        return redirect()->route('app.my.courses');
    }

    private function markPaidAndEnroll(Payment $payment): void
    {
        if ($payment->status === 'paid') return;

        $payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        // catat kupon jika ada
        if (!empty($payment->coupon_id) && (int)$payment->discount_amount > 0) {
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

        $enr = \App\Models\Enrollment::updateOrCreate(
            ['user_id' => $payment->user_id, 'course_id' => $payment->course_id],
            ['status' => 'active', 'activated_at' => now()]
        );

        \Log::info('Enrollment upserted', [
            'payment_id' => $payment->id,
            'user_id'    => $payment->user_id,
            'course_id'  => $payment->course_id,
            'enrollment' => $enr->toArray(),
        ]);
    }
}
