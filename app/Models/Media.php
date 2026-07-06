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

    public function isImage(): bool { return $this->media_type === 'image'; }
    public function isVideo(): bool { return $this->media_type === 'video'; }
    public function isAudio(): bool { return $this->media_type === 'audio'; }
    public function isUrl(): bool   { return $this->media_type === 'url'; }

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
}
