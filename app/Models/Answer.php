<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasUuids;

    /**
     * Primary key akan berupa UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Kolom yang boleh diisi mass assignment.
     */
    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
        'text_answer',
        'is_correct',
    ];

    /**
     * Casting kolom agar konsisten.
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Relasi ke QuizAttempt.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    /**
     * Relasi ke Question.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * Relasi ke Option.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'option_id');
    }
}
