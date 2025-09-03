<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
        'progress',
        'completed_at',
    ];

    protected $dates = ['completed_at'];

    protected $casts = [
        'progress' => 'array', // ✅ sekarang progress bisa array
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
