<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content',      // JSON berisi blok konten
        'content_url',  // JSON berisi daftar link/video/dsb
        'ordering',
        'is_free',
    ];

    protected $casts = [
        'content'     => 'array',   // ⬅️ penting agar tidak tampil mentah
        'content_url' => 'array',   // auto decode/encode JSON <-> array
        'is_free'     => 'boolean',
        'ordering'    => 'integer',
    ];

    protected $attributes = [
        'is_free'     => false,
        'ordering'    => 0,
        'content'     => '[]',   // aman kalau belum diisi
        'content_url' => '[]',
    ];

    /** Scopes */
    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($qq) => $qq->where('title', 'like', "%{$term}%"));
    }

    public function scopeFilterModule($q, $moduleId)
    {
        return $q->when($moduleId, fn($qq) => $qq->where('module_id', $moduleId));
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('ordering')->orderBy('id');
    }

    /** Relations */
    public function module(): BelongsTo     { return $this->belongsTo(Module::class); }
    public function resources(): HasMany    { return $this->hasMany(Resource::class); }
    public function quiz(): HasOne          { return $this->hasOne(Quiz::class); }
    public function progresses(): HasMany   { return $this->hasMany(LessonProgress::class); }
}
