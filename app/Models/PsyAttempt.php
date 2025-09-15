<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class PsyAttempt extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'test_id',
        'user_id',
        'started_at',
        'submitted_at',
        'score_json',
        'result_key',
        'recommendation_text',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score_json'   => 'array',
    ];

    /** ================= Relations ================= */

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsyTest::class, 'test_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PsyAnswer::class, 'attempt_id');
    }

    /** ================= Accessors ================= */

    public function getTotalScoreAttribute(): int
    {
        // Ambil dari score_json kalau ada
        $fromJson = is_array($this->score_json)
            ? ($this->score_json['_total'] ?? null)
            : null;

        if (is_numeric($fromJson)) {
            return (int) $fromJson;
        }

        // Fallback: ambil dari kolom total_score kalau ada
        if (isset($this->attributes['total_score']) && is_numeric($this->attributes['total_score'])) {
            return (int) $this->attributes['total_score'];
        }

        return 0;
    }
}
