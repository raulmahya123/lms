<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
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
        'user_id',
        'membership_id',
        'plan_id',
        'course_id',
        'amount',
        'status',
        'provider',
        'reference',
        'paid_at',
        'snap_token',
        'snap_redirect_url',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    /** ================= Relations ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
