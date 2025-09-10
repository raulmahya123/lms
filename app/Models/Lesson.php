<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    HasOne,
    BelongsToMany
};
use Illuminate\Support\Collection;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content',        // JSON berisi blok konten
        'content_url',    // JSON berisi daftar link/video/dsb
        'ordering',
        'is_free',

        // kolom tambahan di lessons (opsional sesuai migration kamu)
        'drive_emails',   // cache maks 4 email yang diizinkan
        'drive_link',     // link Google Drive utama
        'drive_status',   // pending|approved|rejected
    ];

    protected $casts = [
        'content'       => 'array',
        'content_url'   => 'array',
        'is_free'       => 'boolean',
        'ordering'      => 'integer',
        'drive_emails'  => 'array',   // auto JSON<->array
    ];

    protected $attributes = [
        'is_free'       => false,
        'ordering'      => 0,
        'content'       => '[]',
        'content_url'   => '[]',
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

    /* ===========================
     * Relasi whitelist Google Drive
     * =========================== */

    // ke pivot whitelist (per email)
    public function driveWhitelists(): HasMany
    {
        return $this->hasMany(LessonDriveWhitelist::class);
    }

    // ke user melalui pivot whitelist
    public function driveUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_drive_whitelists')
            ->withPivot(['email', 'status', 'verified_at'])
            ->withTimestamps();
    }

    /* ===========================
     * Helper sinkronisasi whitelist (maks 4 email)
     * ===========================
     * - Normalisasi email -> lowercase+trim
     * - Upsert by (lesson_id, email) agar sesuai unique index di migration
     * - Pertahankan status & verified_at existing (tidak direset)
     * - Hapus entry yang tidak lagi dipilih
     * - Opsional: statusResolver($email) => 'pending'|'approved'|'rejected'
     */
    public function syncDriveEmails(array $emails, ?callable $statusResolver = null): void
    {
        // Normalisasi & batasi 4
        $target = collect($emails)
            ->filter(fn($v) => filled($v))
            ->map(fn($e) => mb_strtolower(trim($e)))
            ->unique()
            ->take(4)
            ->values();

        // Jika kosong, hapus semua whitelist lesson ini dan kosongkan cache
        if ($target->isEmpty()) {
            $this->driveWhitelists()->delete();
            $this->forceFill(['drive_emails' => []])->save();
            return;
        }

        // Existing whitelist keyed by lowercased email
        $existing = $this->driveWhitelists()
            ->get()
            ->keyBy(fn($row) => mb_strtolower($row->email));

        // Map email -> user (jika ada user dengan email tsb)
        $usersByEmail = User::query()
            ->whereIn('email', $target->all())
            ->get()
            ->keyBy(fn($u) => mb_strtolower($u->email));

        $now = now();

        // Build rows untuk upsert (jaga status & verified_at lama)
        $rows = $target->map(function (string $email) use ($existing, $usersByEmail, $statusResolver, $now) {
            $old  = $existing->get($email);
            $user = $usersByEmail->get($email);

            // status: resolver > existing > default pending
            $resolved = $statusResolver ? ($statusResolver($email) ?? null) : null;
            $status   = in_array($resolved, ['pending', 'approved', 'rejected'], true)
                ? $resolved
                : ($old?->status ?? 'pending');

            return [
                'lesson_id'   => $this->id,
                'email'       => $email,
                'user_id'     => $user?->id ?? $old?->user_id,
                'status'      => $status,
                'verified_at' => $old?->verified_at,  // ← aman walau $old null
                'updated_at'  => $now,
                'created_at'  => $old?->created_at ?? $now,
            ];
        })->all();

        // Upsert berdasarkan unique (lesson_id, email)
        LessonDriveWhitelist::upsert(
            $rows,
            ['lesson_id', 'email'],
            ['user_id', 'status', 'verified_at', 'updated_at']
        );

        // Hapus yang tidak lagi ada di input
        $this->driveWhitelists()
            ->whereNotIn('email', $target->all())
            ->delete();

        // Cache daftar email di kolom lessons (opsional)
        $this->forceFill(['drive_emails' => $target->all()])->save();
    }
}
