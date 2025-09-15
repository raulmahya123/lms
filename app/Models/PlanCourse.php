<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanCourse extends Model
{
    use HasUuids;

    /**
     * PK UUID (string).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'plan_id',
        'course_id',
    ];

    /** ================= Relations ================= */

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
