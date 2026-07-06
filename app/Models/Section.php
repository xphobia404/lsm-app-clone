<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Section extends Model
{
    protected $fillable = [
        'course_type_id',
        'slug',
        'title',
        'description',
        'content_mode',   // selalu 'multi'
        'content',        // legacy, nullable
        'pages',          // JSON array of slides
        'thumbnail',
        'order',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'pages'        => 'array',
        'is_published' => 'boolean',
        'order'        => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    // =========================================================================
    // Accessors / Helpers
    // =========================================================================

    /**
     * URL thumbnail section (cover).
     */
    public function getThumbnailUrl(): ?string
    {
        if (!$this->thumbnail) return null;

        // Absolute URL (YouTube embed, external CDN, dll)
        if (str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }

        return Storage::disk('public')->url($this->thumbnail);
    }

    /**
     * Jumlah slide yang dimiliki section ini.
     */
    public function getSlideCountAttribute(): int
    {
        return is_array($this->pages) ? count($this->pages) : 0;
    }

    /**
     * Cek apakah sebuah slide punya minimal 1 media aktif.
     */
    public function slideHasMedia(array $slide): bool
    {
        return !empty($slide['image_url'])
            || !empty($slide['video_url'])
            || !empty($slide['audio_url'])
            || !empty($slide['youtube_url'])
            || !empty($slide['drive_url']);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
