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

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Relations ───────────────────────────────────────────

    /**
     * Sections yang terhubung ke schema ini (pivot: learning_schema_section).
     * Diurutkan berdasarkan section_order di pivot.
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'learning_schema_section')
            ->withPivot('section_order')
            ->withTimestamps()
            ->orderByPivot('section_order');
    }

    /**
     * Users yang enroll ke schema ini (pivot: learning_schema_user).
     */
    public function enrolledUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'learning_schema_user')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps()
            ->orderByPivot('enrolled_at', 'desc');
    }

    public function activeEnrollments(): BelongsToMany
    {
        return $this->enrolledUsers()->wherePivot('status', 'active');
    }
}
