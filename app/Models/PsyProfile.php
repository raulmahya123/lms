<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsyProfile extends Model {
  protected $fillable=['test_id','key','name','min_total','max_total','description'];
  public function test(){ return $this->belongsTo(PsyTest::class,'test_id'); }
}
