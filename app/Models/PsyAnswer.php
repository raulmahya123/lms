<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsyAnswer extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
        'value',
    ];

    /** ================= Relations ================= */

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
        return $this->belongsTo(PsyOption::class, 'option_id');
    }
}
