<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsyOption extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'question_id',
        'label',
        'value',
        'ordering',
    ];

    protected $casts = [
        'ordering' => 'integer',
    ];

    /** ================= Relations ================= */

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsyQuestion::class, 'question_id');
    }
}
