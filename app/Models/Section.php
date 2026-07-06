<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Section extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function learningSchemas(): BelongsToMany
    {
        return $this->belongsToMany(LearningSchema::class, 'learning_schema_section')
            ->withPivot('section_order')
            ->withTimestamps()
            ->orderByPivot('section_order');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class)->orderBy('content_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('created_at');
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('title');
    }
}
