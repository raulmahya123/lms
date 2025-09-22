<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ValidateCouponRequest;
use App\Models\{Coupon, CouponRedemption};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function validateCode(ValidateCouponRequest $r)
    {
        $data = $r->validated();

        $respondInvalid = function (string $reason, int $status = 422) use ($r) {
            Log::warning('coupon.validate.invalid', ['reason' => $reason, 'user_id' => Auth::id()]);
            if ($r->wantsJson() || $r->ajax()) {
                return response()->json(['valid' => false, 'reason' => $reason], $status);
            }
            return back()->withErrors(['coupon' => $reason])->with('coupon_status', 'invalid');
        };

        Log::info('coupon.validate.incoming', [
            'payload' => $data,
            'user_id' => Auth::id(),
        ]);

        // Cari kupon (case-insensitive)
        $coupon = Coupon::whereRaw('LOWER(code) = ?', [strtolower($data['code'])])->first();
        if (!$coupon) return $respondInvalid('Kupon tidak ditemukan', 404);

        // Validitas waktu & kuota
        $now = now();
        if ($coupon->valid_from && $coupon->valid_from->gt($now))  return $respondInvalid('Belum aktif');
        if ($coupon->valid_until && $coupon->valid_until->lt($now)) return $respondInvalid('Kedaluwarsa');
        if ($coupon->usage_limit && $coupon->redemptions()->count() >= $coupon->usage_limit)
            return $respondInvalid('Kuota habis');

        // Sudah dipakai user ini pada konteks yang sama?
        $already = CouponRedemption::where('coupon_id', $coupon->id)
            ->where('user_id', Auth::id())
            ->when($data['plan_id'] ?? null,   fn($q) => $q->where('plan_id',   $data['plan_id']))
            ->when($data['course_id'] ?? null, fn($q) => $q->where('course_id', $data['course_id']))
            ->exists();
        if ($already) return $respondInvalid('Sudah dipakai');

        // Hitung diskon & total akhir
        $amount   = (int) $data['amount']; // rupiah integer
        $discount = (int) round($amount * ($coupon->discount_percent / 100));
        $final    = max(0, $amount - $discount);

        return response()->json([
            'valid'            => true,
            'discount_percent' => $coupon->discount_percent,
            'discount_amount'  => $discount,
            'final_amount'     => $final,
            'coupon_id'        => $coupon->id,
        ]);
    }
}
