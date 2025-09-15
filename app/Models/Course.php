<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};
use Illuminate\Support\Str;

class Course extends Model
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
        'title',
        'description',
        'cover_url',
        'is_published',
        'created_by',
        'is_free',
        'price',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'is_published' => 'boolean',
        'is_free'      => 'boolean',
        'price'        => 'decimal:2',
    ];

    /**
     * Helper: apakah course berbayar.
     */
    public function getIsPaidAttribute(): bool
    {
        return !$this->is_free;
    }

    /**
     * Helper: resolve cover URL jadi absolute.
     */
    public function getCoverUrlResolvedAttribute(): ?string
    {
        $url = $this->cover_url;
        if (!$url) return null;

        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (Str::startsWith($url, ['/storage/', 'storage/'])) {
            return url(ltrim($url, '/'));
        }

        return $url;
    }

    /** ================= Relations ================= */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'course_id', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id');
    }

    public function planCourses(): HasMany
    {
        return $this->hasMany(PlanCourse::class, 'course_id', 'id');
    }

    /**
     * Total lessons lewat modules (courses -> modules -> lessons).
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,  // model tujuan
            Module::class,  // model perantara
            'course_id',    // FK di modules -> courses
            'module_id',    // FK di lessons -> modules
            'id',           // PK di courses
            'id'            // PK di modules
        );
    }
}
