<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Payment, Enrollment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CourseCheckoutController extends Controller
{
    public function checkout(Course $course)
    {
        // kalau sudah enroll, balik
        $already = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')->exists();
        if ($already) {
            return redirect()->route('app.my.courses')
                ->with('info','Kamu sudah ter-enroll di course ini.');
        }

        $clientKey = config('services.midtrans.client_key');
        $isSandbox = !config('services.midtrans.is_production');

        return view('app.courses.checkout', compact('course','clientKey','isSandbox'));
    }

    public function startSnap(Request $r, Course $course)
    {
        $amount = (int) ($course->price ?? 0);

        // gratis → langsung enroll
        if ($amount <= 0) {
            Enrollment::firstOrCreate(
                ['user_id'=>Auth::id(), 'course_id'=>$course->id],
                ['status'=>'active','activated_at'=>now()]
            );
            return response()->json(['free'=>true]);
        }

        // payment pending untuk course ini
        $payment = Payment::firstOrCreate(
            [
                'user_id'   => Auth::id(),
                'course_id' => $course->id,
                'status'    => 'pending',
                'provider'  => 'midtrans',
            ],
            [
                'amount'    => $amount,
                'reference' => 'CRS-'.time().'-'.Auth::id(),
            ]
        );

        // sudah punya token? pakai lagi
        if ($payment->snap_token) {
            return response()->json(['snap_token'=>$payment->snap_token]);
        }

        // Midtrans config
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        if ($payment->amount < 100) {
            return response()->json(['message'=>'Nominal minimal 100 (sandbox).'], 422);
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
                'name'     => $course->title,
            ]],
        ];

        try {
            $trx = Snap::createTransaction($params);
            $payment->snap_token        = $trx->token ?? null;
            $payment->snap_redirect_url = $trx->redirect_url ?? null;
            $payment->save();

            if (!$payment->snap_token) {
                \Log::error('Midtrans: token null (course)', ['response'=>$trx ?? null]);
                return response()->json(['message'=>'Midtrans tidak memberi token'], 422);
            }

            return response()->json(['snap_token'=>$payment->snap_token]);
        } catch (\Throwable $e) {
            \Log::error('Midtrans createTransaction failed (course)', [
                'error'=>$e->getMessage(), 'orderId'=>$payment->reference
            ]);
            return response()->json(['message'=>'Midtrans error: '.$e->getMessage()], 422);
        }
    }
}
