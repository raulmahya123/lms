<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
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
        'code',
        'discount_percent',
        'valid_from',
        'valid_until',
        'usage_limit',
    ];

    /**
     * Casting kolom otomatis.
     */
    protected $casts = [
        'valid_from'      => 'datetime',
        'valid_until'     => 'datetime',
        'discount_percent'=> 'decimal:2',
    ];

    /**
     * Relasi ke CouponRedemption.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }
}
