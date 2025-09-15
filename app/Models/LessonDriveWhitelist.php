<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonDriveWhitelist extends Model
{
    use HasUuids;

    /**
     * Primary key UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $table = 'lesson_drive_whitelists';

    protected $fillable = [
        'lesson_id',
        'user_id',
        'email',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /** ================= Relations ================= */

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
