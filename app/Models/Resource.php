<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'lesson_id',
        'title',
        'url',
        'type',
    ];

    /** ================= Relations ================= */

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
