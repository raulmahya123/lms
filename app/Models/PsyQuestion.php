<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PsyQuestion extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'test_id','prompt','trait_key','qtype','ordering',
    ];

    protected $casts = [
        'ordering' => 'integer',
    ];

    public function test() {
        return $this->belongsTo(PsyTest::class, 'test_id');
    }

    public function options() {
        return $this->hasMany(PsyOption::class, 'question_id');
    }
}
