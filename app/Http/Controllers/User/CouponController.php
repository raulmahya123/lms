<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ValidateCouponRequest;
use App\Models\{Coupon, CouponRedemption};
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function validateCode(ValidateCouponRequest $r)
    {
        $data = $r->validated();
        $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($data['code'])])->first();
        if (!$coupon) return response()->json(['valid'=>false,'reason'=>'Kupon tidak ditemukan'],404);

        $now = now();
        if ($coupon->valid_from && $coupon->valid_from->gt($now)) return response()->json(['valid'=>false,'reason'=>'Belum aktif'],422);
        if ($coupon->valid_until && $coupon->valid_until->lt($now)) return response()->json(['valid'=>false,'reason'=>'Kedaluwarsa'],422);
        if ($coupon->usage_limit && $coupon->redemptions()->count() >= $coupon->usage_limit)
            return response()->json(['valid'=>false,'reason'=>'Kuota habis'],422);

        $already = CouponRedemption::where('coupon_id',$coupon->id)->where('user_id',Auth::id())
            ->when($data['plan_id']??null, fn($q)=>$q->where('plan_id',$data['plan_id']))
            ->when($data['course_id']??null, fn($q)=>$q->where('course_id',$data['course_id']))
            ->exists();
        if ($already) return response()->json(['valid'=>false,'reason'=>'Sudah dipakai'],422);

        $amount   = (float)($data['amount'] ?? 0);
        $discount = round($amount * ($coupon->discount_percent/100), 2);
        $final    = max(0, $amount - $discount);

        return response()->json([
            'valid'=>true,
            'discount_percent'=>$coupon->discount_percent,
            'discount_amount'=>$discount,
            'final_amount'=>$final,
            'coupon_id'=>$coupon->id,
        ]);
    }
}
