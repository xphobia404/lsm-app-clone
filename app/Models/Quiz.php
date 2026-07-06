<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'quiz_order',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'quiz_order' => 'integer',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('quiz_order');
    }

    // Relationships
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Helpers
    public function isCorrect(string $answer): bool
    {
        return strtolower($answer) === $this->correct_answer;
    }

    public function getOptions(): array
    {
        return array_filter([
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ]);
    }
}
