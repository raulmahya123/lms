<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaReply extends Model {
  protected $fillable=['thread_id','user_id','body','is_answer','upvotes'];
  public function thread(){ return $this->belongsTo(QaThread::class,'thread_id'); }
  public function user(){ return $this->belongsTo(User::class); }
}
