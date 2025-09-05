<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckoutPlanRequest;
use App\Http\Requests\User\CheckoutCourseRequest;
use App\Models\{Plan, Course, Payment, Membership, Enrollment, Coupon, CouponRedemption};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkoutPlan(CheckoutPlanRequest $r, Plan $plan)
    {
        $amount = (float) $plan->price;
        $coupon = null; $discount = 0;

        if ($code = $r->input('coupon_code')) {
            $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($code)])->first();
            if ($coupon && $this->couponOk($coupon)) $discount = round($amount*($coupon->discount_percent/100),2);
        }

        $final = max(0, $amount - $discount);

        $payment = DB::transaction(fn() =>
            Payment::create([
                'user_id'=>Auth::id(),
                'plan_id'=>$plan->id,
                'course_id'=>null,
                'amount'=>$final,
                'status'=>$final==0?'paid':'pending',
                'provider'=>'manual',
                'reference'=>'PLN-'.now()->format('YmdHis').'-'.Auth::id(),
                'paid_at'=>$final==0?now():null,
            ])
        );

        if ($payment->status==='paid') {
            $this->grantPlan($plan, $coupon);
            return redirect()->route('app.memberships.index')->with('status','Membership aktif.');
        }
        return back()->with('status','Order dibuat. Ref: '.$payment->reference);
    }

    public function checkoutCourse(CheckoutCourseRequest $r, Course $course)
    {
        $amount = (float) $r->price;
        $coupon = null; $discount = 0;

        if ($code = $r->input('coupon_code')) {
            $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($code)])->first();
            if ($coupon && $this->couponOk($coupon)) $discount = round($amount*($coupon->discount_percent/100),2);
        }

        $final = max(0, $amount - $discount);

        $payment = DB::transaction(fn() =>
            Payment::create([
                'user_id'=>Auth::id(),
                'plan_id'=>null,
                'course_id'=>$course->id,
                'amount'=>$final,
                'status'=>$final==0?'paid':'pending',
                'provider'=>'manual',
                'reference'=>'CRS-'.now()->format('YmdHis').'-'.Auth::id(),
                'paid_at'=>$final==0?now():null,
            ])
        );

        if ($payment->status==='paid') {
            $this->grantCourse($course, $coupon);
            return redirect()->route('app.my.courses')->with('status','Enroll aktif.');
        }
        return back()->with('status','Order dibuat. Ref: '.$payment->reference);
    }

    public function confirm(Request $r, Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        DB::transaction(function() use ($payment, $r) {
            $payment->update([
                'status'=>'paid',
                'paid_at'=>now(),
                'reference'=>$payment->reference.'-OK',
            ]);

            $coupon = null;
            if ($code=$r->input('coupon_code')) {
                $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($code)])->first();
            }

            if ($payment->plan_id) $this->grantPlan(Plan::find($payment->plan_id), $coupon);
            if ($payment->course_id) $this->grantCourse(Course::find($payment->course_id), $coupon);
        });

        return redirect()->route('app.dashboard')->with('status','Pembayaran dikonfirmasi.');
    }

    // Helpers
    protected function couponOk(Coupon $coupon): bool
    {
        $now=now();
        if ($coupon->valid_from && $coupon->valid_from->gt($now)) return false;
        if ($coupon->valid_until && $coupon->valid_until->lt($now)) return false;
        if ($coupon->usage_limit && $coupon->redemptions()->count() >= $coupon->usage_limit) return false;
        return true;
    }

    protected function grantPlan(Plan $plan=null, ?Coupon $coupon=null): void
    {
        if (!$plan) return;
        Membership::updateOrCreate(
            ['user_id'=>Auth::id(),'plan_id'=>$plan->id],
            ['status'=>'active','activated_at'=>now(),'expires_at'=>null]
        );

        $courseIds = $plan->planCourses()->pluck('course_id')->all();
        foreach ($courseIds as $cid) {
            Enrollment::firstOrCreate(
                ['user_id'=>Auth::id(),'course_id'=>$cid],
                ['status'=>'active','activated_at'=>now()]
            );
        }

        if ($coupon) {
            CouponRedemption::create([
                'coupon_id'=>$coupon->id,
                'user_id'=>Auth::id(),
                'plan_id'=>$plan->id,
                'course_id'=>null,
                'used_at'=>now(),
                'amount_discounted'=>null,
            ]);
        }
    }

    protected function grantCourse(Course $course=null, ?Coupon $coupon=null): void
    {
        if (!$course) return;
        Enrollment::firstOrCreate(
            ['user_id'=>Auth::id(),'course_id'=>$course->id],
            ['status'=>'active','activated_at'=>now()]
        );

        if ($coupon) {
            CouponRedemption::create([
                'coupon_id'=>$coupon->id,
                'user_id'=>Auth::id(),
                'plan_id'=>null,
                'course_id'=>$course->id,
                'used_at'=>now(),
                'amount_discounted'=>null,
            ]);
        }
    }
}
