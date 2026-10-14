<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'section_id',
        'total_questions',
        'correct_answers',
        'passing_score',
        'is_passed',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'passing_score' => 'integer',
        'is_passed' => 'boolean',
    ];

    // ── Relationships ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopePassed($query, ?int $passingScore = null)
    {
        if ($passingScore !== null) {
            return $query->whereRaw(
                'total_questions > 0 AND ROUND(correct_answers * 100.0 / total_questions) >= ?',
                [$passingScore]
            );
        }

        return $query->where(function ($q) {
            $q->where('is_passed', true)
                ->orWhereRaw('total_questions > 0 AND ROUND(correct_answers * 100.0 / total_questions) >= COALESCE(passing_score, 70)');
        });
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function getScorePercentage(): int
    {
        if ($this->total_questions === 0) {
            return 0;
        }

        return (int) round(($this->correct_answers / $this->total_questions) * 100);
    }

    public function isPassed(?int $passingScore = null): bool
    {
        if ($passingScore !== null) {
            return $this->getScorePercentage() >= $passingScore;
        }

        if ($this->is_passed !== null) {
            return (bool) $this->is_passed;
        }

        $threshold = $this->passing_score ?? ($this->section?->passing_score ?? 70);

        return $this->getScorePercentage() >= $threshold;
    }
}
