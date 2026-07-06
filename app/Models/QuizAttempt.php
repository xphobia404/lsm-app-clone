<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'score',
        'total_questions',
        'correct_answers',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number'  => 'integer',
            'score'           => 'integer',
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'started_at'      => 'datetime',
            'submitted_at'    => 'datetime',
        ];
    }

    // Scopes
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function getScorePercentage(): float
    {
        if ($this->total_questions === 0) return 0;
        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    public function isPassed(int $passingScore = 70): bool
    {
        return $this->getScorePercentage() >= $passingScore;
    }
}
