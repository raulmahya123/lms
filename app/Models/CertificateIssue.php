<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateIssue extends Model {
  protected $fillable=['serial','template_id','user_id','course_id','assessment_type','assessment_id','score','issued_at','pdf_path'];
  protected $casts=['issued_at'=>'datetime','score'=>'decimal:2'];
  public function template(){ return $this->belongsTo(CertificateTemplate::class,'template_id'); }
  public function user(){ return $this->belongsTo(User::class); }
  public function course(){ return $this->belongsTo(Course::class); }
}
