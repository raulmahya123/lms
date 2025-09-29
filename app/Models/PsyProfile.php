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
        'test_id',
        'key',
        'name',
        'min_total',
        'max_total',
        'description',
        'user_id', 
    ];



    protected $casts = [
        'min_total' => 'integer',
        'max_total' => 'integer',
    ];
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function test(): BelongsTo
    {
        return $this->belongsTo(PsyTest::class, 'test_id');
    }
    public static function findForScore(string $testId, int $total): ?self
    {
        return self::where('test_id', $testId)
            ->where('min_total', '<=', $total)
            ->where('max_total', '>=', $total)
            ->orderBy('min_total')
            ->first();
    }
}
