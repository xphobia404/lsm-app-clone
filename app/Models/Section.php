<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_schema_id',
        'title',
        'description',
        'section_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'     => 'boolean',
            'section_order' => 'integer',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('section_order');
    }

    // Relationships
    public function learningSchema()
    {
        return $this->belongsTo(LearningSchema::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class)->orderBy('content_order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('quiz_order');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('media_order');
    }

    public function progresses()
    {
        return $this->hasMany(UserProgress::class);
    }
}
