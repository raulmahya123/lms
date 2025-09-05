<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsyQuestion extends Model {
  protected $fillable=['test_id','prompt','trait_key','qtype','ordering'];
  public function test(){ return $this->belongsTo(PsyTest::class,'test_id'); }
  public function options(){ return $this->hasMany(PsyOption::class,'question_id'); }
}