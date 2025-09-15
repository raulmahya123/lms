<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaReply extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'thread_id',
        'user_id',
        'body',
        'is_answer',
        'upvotes',
    ];

    protected $casts = [
        'is_answer' => 'boolean',
        'upvotes'   => 'integer',
    ];

    /** ================= Relations ================= */

    public function thread(): BelongsTo
    {
        return $this->belongsTo(QaThread::class, 'thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
