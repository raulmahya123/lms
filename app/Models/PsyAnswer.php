<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsyAnswer extends Model {
  protected $fillable=['attempt_id','question_id','option_id','value'];
  public function attempt(){ return $this->belongsTo(PsyAttempt::class,'attempt_id'); }
  public function question(){ return $this->belongsTo(PsyQuestion::class,'question_id'); }
  public function option(){ return $this->belongsTo(PsyOption::class,'option_id'); }
}
