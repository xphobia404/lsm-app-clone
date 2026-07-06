<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LearningSchema extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ───────────────────────────────────────────────

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'learning_schema_section')
            ->withPivot('section_order')
            ->withTimestamps()
            ->orderByPivot('section_order');
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getActiveSectionsCount(): int
    {
        return $this->sections()->where('is_active', true)->count();
    }
}
