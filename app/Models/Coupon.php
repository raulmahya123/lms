<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'code',
        'discount_percent',
        'valid_from',
        'valid_until',
        'usage_limit',
    ];

    protected $casts = [
        'discount_percent' => 'float',
        'valid_from'       => 'datetime',
        'valid_until'      => 'datetime',
        'usage_limit'      => 'integer',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }
}
