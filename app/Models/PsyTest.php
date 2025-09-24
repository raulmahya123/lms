<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PsyTest extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name','slug','track','type','time_limit_min','is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'time_limit_min' => 'integer',
    ];

    // allowed values (sinkron dengan UI/validation)
    public const TRACKS = ['backend','frontend','fullstack','qa','devops','pm','custom'];
    public const TYPES  = ['likert','mcq','iq','disc','big5','custom'];

    public function questions() {
        return $this->hasMany(PsyQuestion::class, 'test_id');
    }

    public function profiles() {
        return $this->hasMany(PsyProfile::class, 'test_id');
    }

    public function attempts() {
        return $this->hasMany(PsyAttempt::class, 'test_id');
    }
}
