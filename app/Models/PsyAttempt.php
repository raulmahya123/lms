<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsyAttempt extends Model {
  protected $fillable=['test_id','user_id','started_at','submitted_at','score_json','result_key','recommendation_text'];
  protected $casts=['started_at'=>'datetime','submitted_at'=>'datetime','score_json'=>'array'];
  public function test(){ return $this->belongsTo(PsyTest::class,'test_id'); }
  public function user(){ return $this->belongsTo(User::class); }
  public function answers(){ return $this->hasMany(PsyAnswer::class,'attempt_id'); }
}
