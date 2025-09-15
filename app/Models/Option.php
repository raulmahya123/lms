<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Mass assignable.
     */
    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /** ================= Relations ================= */

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
