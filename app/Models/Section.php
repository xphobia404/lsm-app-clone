<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'video',
        'video_type',
        'thumbnail',
        'order',
        'is_published',
        'created_by',
        'course_type_id',
        'pages',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order'        => 'integer',
        'pages'        => 'array',
    ];

    // -------------------------
    // Relations
    // -------------------------

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    /**
     * Semua media milik section ini (polymorphic).
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('order');
    }

    public function images()
    {
        return $this->morphMany(Media::class, 'mediable')
                    ->where('type', 'image')
                    ->orderBy('order');
    }

    // -------------------------
    // Helpers
    // -------------------------

    public function getVideoEmbedUrl(): ?string
    {
        if (!$this->video) return null;

        if ($this->video_type === 'youtube') {
            preg_match('/(?:v=|youtu\.be\/)([\w-]{11})/', $this->video, $m);
            return isset($m[1]) ? "https://www.youtube.com/embed/{$m[1]}" : null;
        }

        return Storage::url($this->video);
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : null;
    }

    public function isCompletedBy(User $user): bool
    {
        return $this->userProgress()
                    ->where('user_id', $user->id)
                    ->where('completed', true)
                    ->exists();
    }
}
