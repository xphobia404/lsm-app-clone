<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'description',
        'content_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'     => 'boolean',
            'content_order' => 'integer',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('content_order');
    }

    // Relationships
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('media_order');
    }
}
