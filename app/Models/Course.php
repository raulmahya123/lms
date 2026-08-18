<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasManyThrough};

class Course extends Model
{
    use HasUuids;

    /** PK UUID (string) */
    public $incrementing = false;
    protected $keyType   = 'string';

    /** Mass assignable fields */
    protected $fillable = [
        'title',
        'description',
        'cover',        // ← cukup satu kolom cover
        'is_published',
        'created_by',
        'is_free',
        'price',
    ];

    /** Casts */
    protected $casts = [
        'is_published' => 'boolean',
        'is_free'      => 'boolean',
        'price'        => 'decimal:2',
    ];

    /** Helper: apakah course berbayar */
    public function getIsPaidAttribute(): bool
    {
        return !$this->is_free;
    }

    /** Helper: resolve cover jadi URL penuh */
    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover) return null;
        return asset('storage/'.$this->cover);
    }

    /* ================= Relations ================= */

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

    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_mentors', 'course_id', 'user_id')
            ->withTimestamps();
    }

    public function scopeManageableBy($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhereHas('mentors', fn ($mentor) => $mentor->whereKey($user->id));
        });
    }

    public function manageableBy(User $user): bool
    {
        return $user->isAdmin()
            || $this->created_by === $user->id
            || $this->mentors()->whereKey($user->id)->exists();
    }

    /** Total lessons lewat modules (courses -> modules -> lessons) */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,
            Module::class,
            'course_id',    // FK di modules
            'module_id',    // FK di lessons
            'id',           // PK di courses
            'id'            // PK di modules
        );
    }
}
