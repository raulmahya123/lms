<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Payment, Enrollment, Membership};
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

    /** Cek akses efektif sebuah enrollment */
    private function hasEffectiveAccess(?Enrollment $enr, bool $hasMembership): bool
    {
        if (!$enr || $enr->status !== 'active') return false;

        // Purchase / Free = selalu efektif
        if (in_array($enr->access_via, ['purchase','free'], true)) return true;

        // Membership = efektif hanya jika membership aktif + belum lewat expires
        if ($enr->access_via === 'membership') {
            $notExpired = is_null($enr->access_expires_at) || now()->lt($enr->access_expires_at);
            return $hasMembership && $notExpired;
        }

        // Fallback lama (tanpa access_via), anggap aktif
        if (empty($enr->access_via)) return true;

        return false;
    }

    public function checkout(Course $course)
    {
        abort_unless($course->is_published, 404);

        $enr = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        $hasMembership      = $this->userHasActiveMembership(Auth::id());
        $hasEffectiveAccess = $this->hasEffectiveAccess($enr, $hasMembership);

        // Kalau MASIH punya akses efektif, tidak perlu checkout
        if ($hasEffectiveAccess) {
            return redirect()->route('app.my.courses')
                ->with('info', 'Kamu sudah ter-enroll dan masih punya akses ke course ini.');
        }

        // Jika akses sudah tidak efektif (mis. membership habis), tetap tampilkan halaman checkout
        $clientKey = config('services.midtrans.client_key');
        $isSandbox = !config('services.midtrans.is_production');

        return view('app.courses.checkout', compact('course', 'clientKey', 'isSandbox', 'hasMembership'));
    }

    public function startSnap(Request $r, Course $course)
    {
        abort_unless($course->is_published, 404);

        $amount = (int) ($course->price ?? 0);

        $enr = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        $hasMembership      = $this->userHasActiveMembership(Auth::id());
        $hasEffectiveAccess = $this->hasEffectiveAccess($enr, $hasMembership);

        // Sudah punya akses efektif? stop.
        if ($hasEffectiveAccess) {
            return response()->json(['message' => 'Kamu sudah ter-enroll dan masih punya akses ke course ini.'], 409);
        }

        // Membership aktif → enroll gratis lagi (akses mengikuti membership)
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

        // Course gratis → enroll gratis (akses permanen)
        if ($amount <= 0) {
            $enr = Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                [
                    'status'            => 'active',
                    'activated_at'      => now(),
                    'access_via'        => 'free',
                    'access_expires_at' => null,
                ]
            );

            if (!$enr->wasRecentlyCreated) {
                $enr->update([
                    'status'            => 'active',
                    'access_via'        => 'free',
                    'access_expires_at' => null,
                ]);
            }

            return response()->json(['free' => true]);
        }

        // ===== Snap Midtrans (per course) =====
        $newReference = 'CRS-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(6));

        $payment = Payment::firstOrCreate(
            [
                'user_id'   => Auth::id(),
                'course_id' => $course->id,
                'status'    => 'pending',
                'provider'  => 'midtrans',
            ],
            [
                'amount'    => $amount,
                'reference' => $newReference,
            ]
        );

        if (empty($payment->reference) || strlen($payment->reference) > 50) {
            $payment->reference = $newReference;
            $payment->save();
        }

        if ($payment->snap_token) {
            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id'   => $payment->reference,
            ]);
        }

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
                'gross_amount' => $payment->amount,
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
            $transactionStatus = $statusResp->transaction_status ?? null;
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

    private function markPaidAndEnroll(Payment $payment): void
    {
        if ($payment->status === 'paid') return;

        $payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

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

        $isFreeCourse  = ((int) ($course->price ?? 0)) <= 0;

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
