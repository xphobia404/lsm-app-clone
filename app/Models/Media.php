<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'media_type',
        'title',
        'description',
        'file_path',
        'url',
        'media_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'media_order' => 'integer',
    ];

    // ── Relationships ───────────────────────────────────────────────

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('media_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('media_order');
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function isImage(): bool        { return $this->media_type === 'image'; }
    public function isVideo(): bool        { return $this->media_type === 'video'; }
    public function isAudio(): bool        { return $this->media_type === 'audio'; }
    public function isYouTube(): bool      { return $this->media_type === 'youtube'; }
    public function isGoogleDrive(): bool  { return $this->media_type === 'google_drive'; }
    public function isUrl(): bool          { return in_array($this->media_type, ['url', 'youtube', 'google_drive']); }

    /**
     * Kembalikan URL yang bisa dirender — file_path (storage) atau url (external).
     */
    public function getDisplayUrl(): ?string
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return $this->url;
    }

    /**
     * Untuk YouTube: kembalikan embed URL.
     */
    public function getYouTubeEmbedUrl(): ?string
    {
        $url = $this->url ?? '';
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)?([\w-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        return null;
    }

    /**
     * Untuk Google Drive: kembalikan preview/embed URL.
     * Format: https://drive.google.com/file/d/{FILE_ID}/preview
     */
    public function getGoogleDriveEmbedUrl(): ?string
    {
        $url = $this->url ?? '';
        // Format: /file/d/{id}/view atau /open?id={id}
        if (preg_match('/\/file\/d\/([\w-]+)/', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }
        if (preg_match('/[?&]id=([\w-]+)/', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }
        return null;
    }
}
