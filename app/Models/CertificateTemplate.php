<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model {
  protected $fillable=['name','background_url','fields_json','svg_json','is_active'];
  protected $casts=['fields_json'=>'array','svg_json'=>'array','is_active'=>'boolean'];
}
