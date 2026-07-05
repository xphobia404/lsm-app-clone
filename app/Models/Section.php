<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_type_id',
        'title',
        'description',
        'content',
        'content_mode',
        'pages',
        'media_type',
        'media_file',
        'media_url',
        'thumbnail',
        'order',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'is_published'   => 'boolean',
        'order'          => 'integer',
        'created_by'     => 'integer',
        'course_type_id' => 'integer',
        'pages'          => 'array',
    ];

    // -------------------------
    // Relations
    // -------------------------

    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------
    // Scopes
    // -------------------------

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order');
    }

    public function scopeForCourseType($query, $courseTypeId)
    {
        if ($courseTypeId) {
            return $query->where('course_type_id', $courseTypeId);
        }
        return $query;
    }

    // -------------------------
    // Accessors
    // -------------------------

    /**
     * Kembalikan array pages yang sudah parsed.
     * image_url dan audio_url selalu di-generate dari path via Storage::url()
     * agar URL tidak hardcode localhost/domain tertentu.
     */
    public function getParsedPagesAttribute(): array
    {
        if ($this->content_mode === 'multi') {
            $raw = is_array($this->pages) ? $this->pages : [];

            return array_map(function (array $page) {
                // Resolve image URL dari image_path jika ada
                if (!empty($page['image_path'])) {
                    $page['image_url'] = Storage::disk('public')->url($page['image_path']);
                } elseif (!empty($page['image_url'])) {
                    // fallback: pakai image_url lama (data lama sebelum fix)
                    $page['image_url'] = $page['image_url'];
                } else {
                    $page['image_url'] = null;
                }

                // Resolve audio URL dari audio_path jika ada
                if (!empty($page['audio_path'])) {
                    $page['audio_url'] = Storage::disk('public')->url($page['audio_path']);
                } else {
                    $page['audio_url'] = null;
                }

                return $page;
            }, $raw);
        }

        // single mode: bungkus ke 1 page supaya viewer universal
        return [[
            'title'     => null,
            'content'   => $this->content,
            'image_url' => null,
            'audio_url' => null,
        ]];
    }

    public function getMediaPlayUrlAttribute(): ?string
    {
        return match ($this->media_type) {
            'youtube'      => $this->media_url,
            'drive'        => $this->media_url,
            'video_upload' => $this->media_file
                ? Storage::disk('public')->url($this->media_file)
                : null,
            'audio_upload' => $this->media_file
                ? Storage::disk('public')->url($this->media_file)
                : null,
            default        => null,
        };
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->media_play_url;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail
            ? Storage::disk('public')->url($this->thumbnail)
            : null;
    }

    // -------------------------
    // Helpers
    // -------------------------

    public function isMultiPage(): bool
    {
        return $this->content_mode === 'multi';
    }

    public function isYoutube(): bool  { return $this->media_type === 'youtube'; }
    public function isDrive(): bool    { return $this->media_type === 'drive'; }
    public function isVideoUpload(): bool { return $this->media_type === 'video_upload'; }
    public function isAudioUpload(): bool { return $this->media_type === 'audio_upload'; }
    public function isUrlBased(): bool { return in_array($this->media_type, ['youtube', 'drive']); }
    public function isFileBased(): bool { return in_array($this->media_type, ['video_upload', 'audio_upload']); }
}
