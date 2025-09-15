<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class PsyQuestion extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'test_id',
        'prompt',
        'trait_key',
        'qtype',
        'ordering',
    ];

    protected $casts = [
        'ordering' => 'integer',
    ];

    /** ================= Relations ================= */

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsyTest::class, 'test_id');
    }

    public function options(): HasMany
    {
        // Likert/MCQ options
        return $this->hasMany(PsyOption::class, 'question_id')
            ->orderBy('ordering')
            ->orderBy('id');
    }

    /** ================= Helpers ================= */

    public function hasOptions(): bool
    {
        return $this->options()->exists();
    }
}
