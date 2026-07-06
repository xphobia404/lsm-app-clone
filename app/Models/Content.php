<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Content extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'content_type',
        'body',
        'url',
        'content_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'content_order' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('content_order');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function isText(): bool  { return $this->content_type === 'text'; }
    public function isVideo(): bool { return $this->content_type === 'video'; }
    public function isFile(): bool  { return $this->content_type === 'file'; }
    public function isUrl(): bool   { return $this->content_type === 'url'; }

    public function getTypeIcon(): string
    {
        return match ($this->content_type) {
            'video' => '🎬',
            'file'  => '📎',
            'url'   => '🔗',
            default => '📄',
        };
    }
}
