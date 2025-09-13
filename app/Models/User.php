<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // === Relasi ke Role ===
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // === Helper role ===
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isMentor(): bool
    {
        return $this->role?->name === 'mentor';
    }

    // Optional: scopes biar query lebih rapi
    public function scopeAdmins($q)  { return $q->whereHas('role', fn($r) => $r->where('name','admin')); }
    public function scopeMentors($q) { return $q->whereHas('role', fn($r) => $r->where('name','mentor')); }

    // === Relasi: user sebagai mentor dari banyak course (pivot) ===
    // Pivot table: course_mentors (user_id, course_id)
    public function mentorOfCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_mentors', 'user_id', 'course_id')
            ->withTimestamps();
    }

    // === Relasi tambahan ===
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Whitelist Google Drive per lesson
    public function whitelistedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_drive_whitelists')
            ->withPivot(['email', 'status', 'verified_at'])
            ->withTimestamps();
    }

    // (Opsional) Mutator password biar otomatis di-hash saat set
    public function setPasswordAttribute($value): void
    {
        if ($value && \Illuminate\Support\Str::startsWith($value, '$2y$') === false) {
            $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
