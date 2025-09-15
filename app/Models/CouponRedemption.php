<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Kolom mass assignable.
     */
    protected $fillable = [
        'coupon_id',
        'user_id',
        'course_id',
        'plan_id',
        'used_at',
        'amount_discounted',
    ];

    /**
     * Casting kolom otomatis.
     */
    protected $casts = [
        'used_at'          => 'datetime',
        'amount_discounted'=> 'decimal:2',
    ];

    /**
     * Relasi ke Coupon.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relasi ke Plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
