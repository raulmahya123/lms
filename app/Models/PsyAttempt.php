<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PsyAttempt extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'test_id',
        'user_id',
        'started_at',
        'submitted_at',
        'score_json',
        'total_score',
        'result_key',
        'recommendation_text',
        'week',
        'month',
    ];


    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score_json'   => 'array',
        'total_score'  => 'integer',
        'week'         => 'integer',
        'month'        => 'integer',
    ];

    public function test()
    {
        return $this->belongsTo(PsyTest::class, 'test_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(PsyAnswer::class, 'attempt_id');
    }
    protected static function booted()
    {
        static::saving(function ($m) {
            if ($m->submitted_at) {
                $m->week  = (int) $m->submitted_at->isoWeek();
                $m->month = (int) $m->submitted_at->month;
            }
        });
    }
}
