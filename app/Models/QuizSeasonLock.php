<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSeasonLock extends Model
{
    protected $table = 'quiz_season_locks';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'season_key',
        'season_start',
        'season_end',
        'attempt_count',
        'last_attempt_at',
    ];

    protected $casts = [
        'season_start'   => 'datetime',
        'season_end'     => 'datetime',
        'last_attempt_at'=> 'datetime',
        'attempt_count'  => 'integer',
    ];

    // Relasi opsional (sesuaikan namespace modelmu)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
