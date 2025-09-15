<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizSeasonLock extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

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
        'season_start'    => 'datetime',
        'season_end'      => 'datetime',
        'last_attempt_at' => 'datetime',
        'attempt_count'   => 'integer',
    ];

    /** ================= Relations ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
