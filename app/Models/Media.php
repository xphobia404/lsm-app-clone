<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'media_type',
        'title',
        'description',
        'file_path',
        'url',
        'media_order',
    ];

    protected function casts(): array
    {
        return [
            'media_order' => 'integer',
        ];
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('media_order');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('media_type', $type);
    }

    // Relationships
    public function mediable()
    {
        return $this->morphTo();
    }

    // Helpers
    public function isFile(): bool
    {
        return !is_null($this->file_path);
    }

    public function isUrl(): bool
    {
        return $this->media_type === 'url';
    }
}
