<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'name',
        'price',
        'period',
        'features',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'features' => 'array',
        'price'    => 'decimal:2',
    ];

    /** ================= Relations ================= */

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function planCourses(): HasMany
    {
        return $this->hasMany(PlanCourse::class);
    }
}
