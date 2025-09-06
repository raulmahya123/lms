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

        $respondInvalid = function (string $reason, int $status = 422) use ($r) {
            if ($r->wantsJson() || $r->ajax()) {
                return response()->json(['valid'=>false,'reason'=>$reason], $status);
            }
            // Request HTML biasa: kirim balik ke halaman sebelumnya
            // Pakai error bag "coupon" atau session flash
            return back()
                ->withErrors(['coupon' => $reason]) // $errors->first('coupon') di Blade
                ->withInput()
                ->with('coupon_status', 'invalid'); // opsional flag
        };

        $coupon = Coupon::whereRaw('LOWER(code)=?', [strtolower($data['code'])])->first();
        if (!$coupon) return $respondInvalid('Kupon tidak ditemukan', 404);

        $now = now();
        if ($coupon->valid_from && $coupon->valid_from->gt($now))      return $respondInvalid('Belum aktif');
        if ($coupon->valid_until && $coupon->valid_until->lt($now))     return $respondInvalid('Kedaluwarsa');
        if ($coupon->usage_limit && $coupon->redemptions()->count() >= $coupon->usage_limit)
            return $respondInvalid('Kuota habis');

        $already = CouponRedemption::where('coupon_id',$coupon->id)->where('user_id',Auth::id())
            ->when($data['plan_id']??null, fn($q)=>$q->where('plan_id',$data['plan_id']))
            ->when($data['course_id']??null, fn($q)=>$q->where('course_id',$data['course_id']))
            ->exists();
        if ($already) return $respondInvalid('Sudah dipakai');

        $amount   = (float)($data['amount'] ?? 0);
        $discount = round($amount * ($coupon->discount_percent/100), 2);
        $final    = max(0, $amount - $discount);

        // Sukses → JSON untuk AJAX, atau flash ke view untuk HTML
        if ($r->wantsJson() || $r->ajax()) {
            return response()->json([
                'valid'=>true,
                'discount_percent'=>$coupon->discount_percent,
                'discount_amount'=>$discount,
                'final_amount'=>$final,
                'coupon_id'=>$coupon->id,
            ]);
        }

        return back()
            ->with([
                'coupon_status'   => 'valid',
                'coupon_id'       => $coupon->id,
                'discount_percent'=> $coupon->discount_percent,
                'discount_amount' => $discount,
                'final_amount'    => $final,
            ]);
    }
}
