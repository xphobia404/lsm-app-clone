<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    // -------------------------
    // Relations
    // -------------------------

    public function mediable()
    {
        return $this->morphTo();
    }

    // -------------------------
    // Accessors
    // -------------------------

    /**
     * URL publik file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Hapus file fisik saat record dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (Media $media) {
            Storage::disk($media->disk)->delete($media->path);
        });
    }
}
