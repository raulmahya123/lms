<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestIq extends Model
{
    protected $table = 'test_iq';

    protected $fillable = [
        'title',
        'description',
        'questions',         // JSON soal + opsi + kunci
        'is_active',
        'duration_minutes',
        'submissions',       // JSON hasil user (opsional, tetap 1 model)
    ];

    protected $casts = [
        'questions'         => 'array',
        'submissions'       => 'array',
        'is_active'         => 'boolean',
        'duration_minutes'  => 'integer',
    ];

    /** Helper: total soal */
    public function totalQuestions(): int
    {
        return count($this->questions ?? []);
    }
}
