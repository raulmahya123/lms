<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsyAnswer extends Model
{
    use HasUuids;

    // opsional, biar eksplisit
    protected $table = 'psy_answers';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'attempt_id', 'question_id', 'option_id', 'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    /* ================= Relations ================= */

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PsyAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsyQuestion::class, 'question_id');
    }

    public function option(): BelongsTo
    {
        // withDefault supaya $this->option selalu object (tidak undefined)
        return $this->belongsTo(PsyOption::class, 'option_id')->withDefault([
            'value' => null,
            'label' => null,
        ]);
    }

    /* =============== Helpers & Accessors =============== */

    /**
     * Nilai jawaban final untuk perhitungan:
     * - Jika kolom 'value' terisi (Likert numeric langsung) → pakai itu
     * - Jika tidak, dan ada option → pakai option->value
     * - Jika keduanya kosong → 0
     */
    public function getScoreAttribute(): int
    {
        if (!is_null($this->value)) {
            return (int) $this->value;
        }
        if ($this->relationLoaded('option')) {
            return (int) ($this->option->value ?? 0);
        }
        // fallback kalau belum di-load (hindari extra query di loop besar)
        return (int) PsyOption::whereKey($this->option_id)->value('value') ?: 0;
    }

    /* ===================== Scopes ===================== */

    public function scopeForAttempt($q, string $attemptId)
    {
        return $q->where('attempt_id', $attemptId);
    }

    public function scopeForTest($q, string $testId)
    {
        return $q->whereHas('question', fn($qq) => $qq->where('test_id', $testId));
    }
}
