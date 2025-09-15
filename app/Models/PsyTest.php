<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsyTest extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'name',
        'slug',
        'track',
        'type',
        'time_limit_min',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'time_limit_min' => 'integer',
    ];

    /** ================= Relations ================= */

    public function questions(): HasMany
    {
        return $this->hasMany(PsyQuestion::class, 'test_id');
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(PsyProfile::class, 'test_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PsyAttempt::class, 'test_id');
    }
}
