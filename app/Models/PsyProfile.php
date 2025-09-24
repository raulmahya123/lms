<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsyProfile extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'test_id','key','name','min_total','max_total','description',
    ];

    protected $casts = [
        'min_total' => 'integer',
        'max_total' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsyTest::class, 'test_id');
    }
}
