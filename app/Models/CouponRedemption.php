<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'course_id',
        'plan_id',
        'used_at',
        'amount_discounted',
    ];

    // ✅ Gunakan casts, bukan $dates
    protected $casts = [
        'used_at' => 'datetime',
        'amount_discounted' => 'decimal:2', // opsional, biar format angka rapi
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
