<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CourseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // =========================================================================
    // Hooks
    // =========================================================================

    protected static function booted(): void
    {
        static::creating(function (CourseType $ct) {
            if (empty($ct->slug)) {
                $ct->slug = Str::slug($ct->name) . '-' . time();
            }
        });

        static::updating(function (CourseType $ct) {
            if ($ct->isDirty('name') && empty($ct->slug)) {
                $ct->slug = Str::slug($ct->name) . '-' . time();
            }
        });
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Sections yang dimiliki course type ini, terurut.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    /**
     * Sections yang sudah dipublish.
     */
    public function publishedSections(): HasMany
    {
        return $this->hasMany(Section::class)
                    ->where('is_published', true)
                    ->orderBy('order');
    }

    /**
     * Many-to-many: user yang assign ke course type ini.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_type_user')
                    ->withTimestamps();
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Total soal quiz di seluruh section course type ini.
     */
    public function getTotalQuizCountAttribute(): int
    {
        return $this->sections()
                    ->withCount('quizzes')
                    ->get()
                    ->sum('quizzes_count');
    }

    /**
     * Total section yang sudah published.
     */
    public function getPublishedSectionCountAttribute(): int
    {
        return $this->sections()->where('is_published', true)->count();
    }
}
