<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Section extends Model
{
    protected $fillable = [
        'course_type_id',
        'created_by',
        'slug',
        'title',
        'description',
        'content',
        'pages',
        'thumbnail',
        'passing_score',
        'order',
        'is_published',
    ];

    protected $casts = [
        'pages'         => 'array',
        'is_published'  => 'boolean',
        'order'         => 'integer',
        'passing_score' => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    /**
     * User yang membuat section ini.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Media polymorphic (gambar/video/audio cover section).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('order');
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

    // =========================================================================
    // Accessors / Helpers
    // =========================================================================

    /**
     * URL thumbnail section.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

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
     * Jumlah soal quiz section ini.
     */
    public function getQuizCountAttribute(): int
    {
        return $this->quizzes()->count();
    }

    /**
     * Cek apakah sebuah slide punya media aktif.
     */
    public function slideHasMedia(array $slide): bool
    {
        return ! empty($slide['image_url'])
            || ! empty($slide['video_url'])
            || ! empty($slide['audio_url'])
            || ! empty($slide['youtube_url'])
            || ! empty($slide['drive_url']);
    }

    /**
     * Progress user tertentu pada section ini.
     */
    public function progressFor(int $userId): ?UserProgress
    {
        return $this->userProgress()->where('user_id', $userId)->first();
    }
}
