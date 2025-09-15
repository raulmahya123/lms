<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateIssue extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Mass assignable.
     */
    protected $fillable = [
        'serial',
        'template_id',
        'user_id',
        'course_id',
        'assessment_type',
        'assessment_id',
        'score',
        'issued_at',
        'pdf_path',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'score'     => 'decimal:2',
    ];

    /**
     * Relasi ke CertificateTemplate.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
