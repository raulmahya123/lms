<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CertificateTemplate extends Model
{
    use HasUuids;

    /**
     * Primary key UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'name',
        'background_url',
        'fields_json',
        'svg_json',
        'is_active',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'fields_json' => 'array',
        'svg_json'    => 'array',
        'is_active'   => 'boolean',
    ];
}
