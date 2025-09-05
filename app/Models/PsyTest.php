<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsyTest extends Model {
  protected $fillable=['name','slug','track','type','time_limit_min','is_active'];
  protected $casts=['is_active'=>'boolean'];
  public function questions(){ return $this->hasMany(PsyQuestion::class,'test_id'); }
  public function profiles(){ return $this->hasMany(PsyProfile::class,'test_id'); }
}
