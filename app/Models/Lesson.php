<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content',
        'content_url',
        'ordering',
        'is_free',
    ];

    // ⬇️ Tambahan penting
    protected $casts = [
        'content_url' => 'array',   // auto decode/encode JSON <-> array
        'is_free'     => 'boolean', // pastikan true/false
        'ordering'    => 'integer',
    ];

    // (Opsional) default nilai
    protected $attributes = [
        'is_free'  => false,
        'ordering' => 0,
    ];

    // (Opsional) scopes buat controller/index
    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($qq) => $qq->where('title', 'like', "%{$term}%"));
    }

    public function scopeFilterModule($q, $moduleId)
    {
        return $q->when($moduleId, fn($qq) => $qq->where('module_id', $moduleId));
    }

    // Relations
    public function module(): BelongsTo { return $this->belongsTo(Module::class); }
    public function resources(): HasMany { return $this->hasMany(Resource::class); }
    public function quiz(): HasOne { return $this->hasOne(Quiz::class); }
    public function progresses(): HasMany { return $this->hasMany(LessonProgress::class); }
}
