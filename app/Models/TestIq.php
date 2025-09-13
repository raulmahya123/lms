<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestIq extends Model
{
    use HasFactory;

    protected $table = 'test_iq';

    protected $fillable = [
        'title',
        'description',
        'questions',         // JSON soal + opsi + kunci
        'is_active',
        'duration_minutes',
        'cooldown_value',    // angka: 1, 2, 3, ...
        'cooldown_unit',     // 'day' | 'week' | 'month'
        'submissions',       // JSON hasil user (opsional)
    ];

    protected $casts = [
        'questions'         => 'array',
        'submissions'       => 'array',
        'is_active'         => 'boolean',
        'duration_minutes'  => 'integer',
        'cooldown_value'    => 'integer',
        // cooldown_unit tetap string => tidak perlu cast
    ];

    /** Total jumlah soal */
    public function totalQuestions(): int
    {
        return count($this->questions ?? []);
    }

    /**
     * Kapan user tertentu boleh tes lagi?
     * - Jika belum pernah submit → sekarang (now()).
     * - Jika sudah, tambahkan cool-down sesuai unit & value.
     */
    public function nextAvailableAtFor(int $userId): Carbon
    {
        $last = collect($this->submissions ?? [])
            ->where('user_id', $userId)
            ->sortByDesc('submitted_at')
            ->first();

        if (! $last || empty($last['submitted_at'])) {
            return now(); // belum pernah ikut → boleh sekarang
        }

        $start = Carbon::parse($last['submitted_at']);

        return match ($this->cooldown_unit) {
            'day'   => $start->copy()->addDays($this->cooldown_value),
            'week'  => $start->copy()->addWeeks($this->cooldown_value),
            default => $start->copy()->addMonths($this->cooldown_value), // 'month'
        };
    }

    /** Apakah user boleh ikut tes sekarang? */
    public function canAttempt(int $userId): bool
    {
        return now()->greaterThanOrEqualTo($this->nextAvailableAtFor($userId));
    }

    /** (Opsional) Ambil submission terakhir user ini untuk tes ini */
    public function lastSubmissionFor(int $userId): ?array
    {
        return collect($this->submissions ?? [])
            ->where('user_id', $userId)
            ->sortByDesc('submitted_at')
            ->first() ?: null;
    }
}
