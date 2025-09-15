<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
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
        'user_id',
        'course_id',
        'status',
        'activated_at',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'activated_at' => 'datetime',
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

    /** ================= Accessors ================= */

    /**
     * Hitung persentase progress enrollment.
     */
    public function getProgressPercentAttribute(): int
    {
        $total = (int) ($this->total_lessons ?? 0);
        $done  = (int) ($this->done_lessons  ?? 0);

        return $total > 0 ? (int) round($done * 100 / $total) : 0;
    }
}
