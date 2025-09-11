<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'cover_url',
        'is_published',
        'created_by',
        'is_free',
        'price',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_free' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Helper opsional
    public function getIsPaidAttribute(): bool
    {
        return !$this->is_free;
    }

    public function getCoverUrlResolvedAttribute(): ?string
    {
        $url = $this->cover_url;
        if (!$url) return null;

        // kalau sudah http/https -> langsung pakai
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        // kalau /storage/... atau storage/... -> prefix dengan APP_URL
        if (Str::startsWith($url, ['/storage/', 'storage/'])) {
            return url(ltrim($url, '/')); // jadikan absolute
        }

        // fallback: kembalikan apa adanya
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
     * Total lessons lewat modules (courses -> modules -> lessons)
     * Dipakai agar withCount('lessons as lessons_count') bisa jalan.
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,   // model tujuan
            Module::class,   // model perantara
            'course_id',     // FK di modules yang mengarah ke courses
            'module_id',     // FK di lessons yang mengarah ke modules
            'id',            // PK di courses
            'id'             // PK di modules
        );
    }
}
