<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    HasOne,
    BelongsToMany
};

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'about',
        'syllabus',
        'reviews',
        'tools',
        'created_by',
        'benefits',
        'content',
        'content_url',
        'ordering',
        'is_free',
        'drive_emails',
        'drive_link',
    ];

    protected $casts = [
        'content'       => 'array',
        'content_url'   => 'array',
        'is_free'       => 'boolean',
        'ordering'      => 'integer',
        'drive_emails'  => 'array',
        'reviews'       => 'array',   // JSON → array
        'tools'         => 'array',   // JSON → array
    ];

    protected $attributes = [
        'is_free'       => false,
        'ordering'      => 0,
        'content'       => '[]',
        'content_url'   => '[]',
        'reviews'       => '[]',
        'tools'         => '[]',
    ];

    /* ===========================
     * Scopes
     * =========================== */
    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($qq) => $qq->where('title', 'like', "%{$term}%"));
    }

    public function scopeFilterModule($q, $moduleId)
    {
        return $q->when($moduleId, fn($qq) => $qq->where('module_id', $moduleId));
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('ordering')->orderBy('id');
    }

    /* ===========================
     * Relations utama
     * =========================== */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    // pembuat lesson
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ===========================
     * Relasi whitelist Google Drive
     * =========================== */
    public function driveWhitelists(): HasMany
    {
        return $this->hasMany(LessonDriveWhitelist::class);
    }

    public function driveUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_drive_whitelists')
            ->withPivot(['email', 'status', 'verified_at'])
            ->withTimestamps();
    }

    /* ===========================
     * Helper sinkronisasi whitelist
     * =========================== */
    public function syncDriveEmails(array $emails, ?callable $statusResolver = null): void
    {
        $target = collect($emails)
            ->filter(fn($v) => filled($v))
            ->map(fn($e) => mb_strtolower(trim($e)))
            ->unique()
            ->take(4)
            ->values();

        if ($target->isEmpty()) {
            $this->driveWhitelists()->delete();
            $this->forceFill(['drive_emails' => []])->save();
            return;
        }

        $existing = $this->driveWhitelists()
            ->get()
            ->keyBy(fn($row) => mb_strtolower($row->email));

        $usersByEmail = User::query()
            ->whereIn('email', $target->all())
            ->get()
            ->keyBy(fn($u) => mb_strtolower($u->email));

        $now = now();

        $rows = $target->map(function (string $email) use ($existing, $usersByEmail, $statusResolver, $now) {
            $old  = $existing->get($email);
            $user = $usersByEmail->get($email);

            $resolved = $statusResolver ? ($statusResolver($email) ?? null) : null;
            $status   = in_array($resolved, ['pending', 'approved', 'rejected'], true)
                ? $resolved
                : ($old?->status ?? 'pending');

            return [
                'lesson_id'   => $this->id,
                'email'       => $email,
                'user_id'     => $user?->id ?? $old?->user_id,
                'status'      => $status,
                'verified_at' => $old?->verified_at,
                'updated_at'  => $now,
                'created_at'  => $old?->created_at ?? $now,
            ];
        })->all();

        LessonDriveWhitelist::upsert(
            $rows,
            ['lesson_id', 'email'],
            ['user_id', 'status', 'verified_at', 'updated_at']
        );

        $this->driveWhitelists()
            ->whereNotIn('email', $target->all())
            ->delete();

        $this->forceFill(['drive_emails' => $target->all()])->save();
    }
}
