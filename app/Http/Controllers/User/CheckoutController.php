<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckoutPlanRequest;
use App\Http\Requests\User\CheckoutCourseRequest;
use App\Models\{Plan, Course, Payment, Membership, Enrollment, Coupon, CouponRedemption};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Str;



class CheckoutController extends Controller
{
    public function checkoutPlan(CheckoutPlanRequest $r, Plan $plan, MidtransService $midtrans)
    {
        $amount = (float) $plan->price;
        $coupon = null;
        $discount = 0;

        if ($code = $r->input('coupon_code')) {
            $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($code)])->first();
            if ($coupon && $this->couponOk($coupon)) {
                $discount = round($amount * ($coupon->discount_percent / 100), 2);
            }
        }

        $final = max(0, $amount - $discount);

        // buat payment
        $payment = DB::transaction(
            fn() =>
            Payment::create([
                'user_id'   => Auth::id(),
                'plan_id'   => $plan->id,
                'course_id' => null,
                'amount'    => $final,
                'status'    => 'pending',
                'provider'  => 'midtrans',
                'reference' => 'PLN-' . now()->format('YmdHis') . '-' . Auth::id() . '-' . Str::random(6),
            ])
        );

        // build params untuk Snap
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

        $snap = $midtrans->createSnap($params);

        $payment->update([
            'snap_token'        => $snap['token'],
            'snap_redirect_url' => $snap['redirect_url'],
        ]);

        return view('app.payments.snap', [
            'payment'   => $payment,
            'snapToken' => $payment->snap_token,
            'clientKey' => config('services.midtrans.client_key'),
            'isSandbox' => !config('services.midtrans.is_production'),
        ]);
    }

    public function confirm(Request $r, Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        DB::transaction(function () use ($payment, $r) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'reference' => $payment->reference . '-OK',
            ]);

            $coupon = null;
            if ($code = $r->input('coupon_code')) {
                $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($code)])->first();
            }

            if ($payment->plan_id) $this->grantPlan(Plan::find($payment->plan_id), $coupon);
            if ($payment->course_id) $this->grantCourse(Course::find($payment->course_id), $coupon);
        });

        return redirect()->route('app.dashboard')->with('status', 'Pembayaran dikonfirmasi.');
    }

    // Helpers
    protected function couponOk(Coupon $coupon): bool
    {
        $now = now();
        if ($coupon->valid_from && $coupon->valid_from->gt($now)) return false;
        if ($coupon->valid_until && $coupon->valid_until->lt($now)) return false;
        if ($coupon->usage_limit && $coupon->redemptions()->count() >= $coupon->usage_limit) return false;
        return true;
    }

    protected function grantPlan(Plan $plan = null, ?Coupon $coupon = null): void
    {
        if (!$plan) return;
        Membership::updateOrCreate(
            ['user_id' => Auth::id(), 'plan_id' => $plan->id],
            ['status' => 'active', 'activated_at' => now(), 'expires_at' => null]
        );

        $courseIds = $plan->planCourses()->pluck('course_id')->all();
        foreach ($courseIds as $cid) {
            Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $cid],
                ['status' => 'active', 'activated_at' => now()]
            );
        }

        if ($coupon) {
            CouponRedemption::create([
                'coupon_id' => $coupon->id,
                'user_id' => Auth::id(),
                'plan_id' => $plan->id,
                'course_id' => null,
                'used_at' => now(),
                'amount_discounted' => null,
            ]);
        }
    }

    protected function grantCourse(Course $course = null, ?Coupon $coupon = null): void
    {
        if (!$course) return;
        Enrollment::firstOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $course->id],
            ['status' => 'active', 'activated_at' => now()]
        );

        if ($coupon) {
            CouponRedemption::create([
                'coupon_id' => $coupon->id,
                'user_id' => Auth::id(),
                'plan_id' => null,
                'course_id' => $course->id,
                'used_at' => now(),
                'amount_discounted' => null,
            ]);
        }
    }
}
