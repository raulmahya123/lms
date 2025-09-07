<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = ['user_id', 'course_id', 'status', 'activated_at'];

    protected $dates = ['activated_at'];
    protected $casts = [
        'activated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    // di App\Models\Enrollment
    public function getProgressPercentAttribute(): int
    {
        $total = (int) ($this->total_lessons ?? 0);
        $done  = (int) ($this->done_lessons  ?? 0);
        return $total > 0 ? (int) round($done * 100 / $total) : 0;
    }
}
