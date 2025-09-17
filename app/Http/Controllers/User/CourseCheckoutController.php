<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Payment, Enrollment};
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

        $amount = (int) ($course->price ?? 0);

        // Guard: kalau sudah active, hentikan
        $already = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')->exists();
        if ($already) {
            return response()->json(['message' => 'Kamu sudah ter-enroll di course ini.'], 409);
        }

        // Guard: kalau sudah ada payment 'paid' untuk course ini, hentikan
        $paidExists = Payment::where([
            'user_id'   => Auth::id(),
            'course_id' => $course->id,
            'provider'  => 'midtrans',
            'status'    => 'paid',
        ])->exists();
        if ($paidExists) {
            return response()->json(['message' => 'Pembayaran course ini sudah berhasil.'], 409);
        }

        // Kursus gratis → langsung enroll
        if ($amount <= 0) {
            Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                ['status' => 'active', 'activated_at' => now()]
            );
            return response()->json(['free' => true]);
        }

        // 1) Buat reference/order_id pendek & unik
        $newReference = 'CRS-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(6));

        // 2) Ambil/buat Payment pending
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

        // 3) Pastikan reference aman (tak kosong/tak kepanjangan)
        if (empty($payment->reference) || strlen($payment->reference) > 50) {
            $payment->reference = $newReference;
            $payment->save();
        }

        // 4) Reuse token jika masih ada
        if ($payment->snap_token) {
            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id'   => $payment->reference, // penting utk finish page
            ]);
        }

        // 5) Midtrans config
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        if ($payment->amount < 100) {
            return response()->json(['message' => 'Nominal minimal 100 (sandbox).'], 422);
        }

        // 6) Buat transaksi Snap
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
                'order_id'   => $payment->reference, // ← dikirim ke frontend
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans createTransaction failed (course)', [
                'error'   => $e->getMessage(),
                'orderId' => $payment->reference,
                'len'     => strlen($payment->reference),
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

    /** Helper: set paid + enroll user ke course */
    private function markPaidAndEnroll(Payment $payment): void
    {
        if ($payment->status === 'paid') return;

        $payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        $enr = Enrollment::updateOrCreate(
            ['user_id' => $payment->user_id, 'course_id' => $payment->course_id],
            ['status' => 'active', 'activated_at' => now()]
        );

        Log::info('Enrollment upserted', [
            'payment_id' => $payment->id,
            'user_id'    => $payment->user_id,
            'course_id'  => $payment->course_id,
            'enrollment' => $enr->toArray(),
        ]);
    }
}
