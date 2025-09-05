<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaThread extends Model {
  protected $fillable=['user_id','course_id','lesson_id','title','body','status'];
  public function user(){ return $this->belongsTo(User::class); }
  public function course(){ return $this->belongsTo(Course::class); }
  public function lesson(){ return $this->belongsTo(Lesson::class); }
  public function replies(){ return $this->hasMany(QaReply::class,'thread_id'); }
}