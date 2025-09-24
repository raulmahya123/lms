<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsyOption extends Model
{
    use HasUuids;

    protected $table = 'psy_options';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'question_id', 'label', 'value', 'ordering',
    ];

    protected $casts = [
        'value'    => 'integer',
        'ordering' => 'integer',
    ];

    /* ================= Relations ================= */

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsyQuestion::class, 'question_id');
    }

    /* ================= Scopes ================= */

    /**
     * Selalu urutkan option by ordering lalu created_at
     */
    public function scopeOrdered($q)
    {
        return $q->orderBy('ordering')->orderBy('created_at');
    }

    /* ================= Accessors ================= */

    /**
     * Ringkasan label (misalnya untuk debug/log)
     */
    public function getDisplayTextAttribute(): string
    {
        return "{$this->label} ({$this->value})";
    }
}
