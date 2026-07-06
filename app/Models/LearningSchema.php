<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Relations ─────────────────────────────────────────────

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('section_order');
    }

    /**
     * Users yang enroll ke schema ini.
     * Akses pivot: $user->pivot->enrolled_at, $user->pivot->status
     */
    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'learning_schema_user')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps()
            ->orderByPivot('enrolled_at', 'desc');
    }

    public function activeEnrollments()
    {
        return $this->enrolledUsers()->wherePivot('status', 'active');
    }
}
