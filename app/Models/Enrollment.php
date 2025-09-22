<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'activated_at',
        // NEW:
        'access_via',         // purchase|membership|free
        'access_expires_at',  // null = tak terbatas
    ];

    protected $casts = [
        'activated_at'      => 'datetime',
        'access_expires_at' => 'datetime',
    ];

    /** ================= Relations ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** ================= Access Helpers ================= */

    /**
     * Cek apakah enrollment ini masih boleh mengakses konten,
     * dengan mempertimbangkan status membership user saat ini.
     */
    public function hasEffectiveAccess(bool $hasActiveMembership): bool
    {
        if ($this->status !== 'active') return false;

        // Membership-based: butuh membership aktif + (jika ada) belum lewat access_expires_at
        if ($this->access_via === 'membership') {
            $notExpired = is_null($this->access_expires_at) || $this->access_expires_at->isFuture();
            return $hasActiveMembership && $notExpired;
        }

        // Purchase / Free: akses permanen
        return true;
    }

    /** ================= Presenters ================= */

    public function getProgressPercentAttribute(): int
    {
        $total = (int) ($this->total_lessons ?? 0);
        $done  = (int) ($this->done_lessons  ?? 0);
        return $total > 0 ? (int) round($done * 100 / $total) : 0;
    }
}
