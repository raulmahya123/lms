<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progresses';

    protected $fillable = [
        'lesson_id',
        'user_id',
        'progress',     // JSON: mis. ['watched' => true]
        'completed_at', // nullable timestamp
    ];

    protected $casts = [
        'progress'     => 'array',
        'completed_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Quality-of-life accessor (opsional)
    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->completed_at);
    }

    public function getWatchedAttribute(): bool
    {
        return (bool) data_get($this->progress, 'watched', false);
    }
}
