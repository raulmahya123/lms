<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'cover_url',
        'is_published',
        'created_by',
    ];

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
