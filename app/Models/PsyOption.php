<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsyOption extends Model {
  protected $fillable=['question_id','label','value','ordering'];
  public function question(){ return $this->belongsTo(PsyQuestion::class,'question_id'); }
}