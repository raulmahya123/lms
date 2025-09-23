<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TestIq extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'test_iq';

    protected $fillable = [
        'title',
        'description',
        'questions',         // JSON soal + opsi + kunci
        'is_active',
        'duration_minutes',
        'cooldown_value',    // angka: 1, 2, 3, ...
        'cooldown_unit',     // 'day' | 'week' | 'month'
        'submissions',       // JSON hasil user (opsional)
        'meta',              // <-- penting untuk norm_table, level, max_questions
    ];

    protected $casts = [
        'questions'        => 'array',
        'submissions'      => 'array',
        'is_active'        => 'boolean',
        'duration_minutes' => 'integer',
        'cooldown_value'   => 'integer',
        'meta'             => 'array',
    ];

    /** ================= Helpers ================= */

    public function totalQuestions(): int
    {
        return count($this->questions ?? []);
    }

    /**
     * Kapan user tertentu boleh tes lagi?
     */
    public function nextAvailableAtFor(string $userId): Carbon
    {
        $last = collect($this->submissions ?? [])
            ->where('user_id', $userId)
            ->sortByDesc('submitted_at')
            ->first();

        if (!$last || empty($last['submitted_at'])) {
            return now(); // belum pernah ikut → boleh sekarang
        }

        $start = Carbon::parse($last['submitted_at']);

        return match ($this->cooldown_unit) {
            'day'   => $start->copy()->addDays((int)$this->cooldown_value),
            'week'  => $start->copy()->addWeeks((int)$this->cooldown_value),
            default => $start->copy()->addMonths((int)$this->cooldown_value), // 'month'
        };
    }

    public function canAttempt(string $userId): bool
    {
        return now()->greaterThanOrEqualTo($this->nextAvailableAtFor($userId));
    }

    public function lastSubmissionFor(string $userId): ?array
    {
        return collect($this->submissions ?? [])
            ->where('user_id', $userId)
            ->sortByDesc('submitted_at')
            ->first() ?: null;
    }
}
    