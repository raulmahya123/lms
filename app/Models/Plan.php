<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'period', 'features'];

    protected $casts = [
        'features' => 'array',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function planCourses(): HasMany
    {
        return $this->hasMany(PlanCourse::class);
    }
}
