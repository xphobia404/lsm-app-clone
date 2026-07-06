<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'order',
    ];

    protected $casts = [
        'size'  => 'integer',
        'order' => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * URL publik file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Ukuran file dalam format human-readable.
     * Contoh: "1.2 MB", "340 KB"
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1) . ' MB';
        }
        if ($bytes >= 1_024) {
            return round($bytes / 1_024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Cek apakah media bertipe gambar.
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Cek apakah media bertipe video.
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Cek apakah media bertipe audio.
     */
    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    // =========================================================================
    // Hooks
    // =========================================================================

    protected static function booted(): void
    {
        /**
         * Hapus file fisik dari storage saat record dihapus.
         */
        static::deleting(function (Media $media) {
            Storage::disk($media->disk)->delete($media->path);
        });
    }
}
