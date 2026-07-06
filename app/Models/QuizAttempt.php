<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section_id',
        'attempt_number',
        'answers',
        'score',
        'score_percent',
        'passed',
        'submitted_at',
    ];

    protected $casts = [
        'passed'         => 'boolean',
        'attempt_number' => 'integer',
        'score'          => 'integer',
        'score_percent'  => 'integer',
        'answers'        => 'array',
        'submitted_at'   => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Hanya attempt yang lulus.
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    /**
     * Hanya attempt yang tidak lulus.
     */
    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    /**
     * Attempt terbaru per section per user.
     */
    public function scopeLatestPerSection($query)
    {
        return $query->orderBy('attempt_number', 'desc');
    }

    // =========================================================================
    // Accessors / Helpers
    // =========================================================================

    /**
     * Label score untuk ditampilkan di view.
     * Contoh: "8/10 (80%)"
     */
    public function getScoreLabelAttribute(): string
    {
        $total = $this->section?->quizzes()->count() ?? 0;
        return "{$this->score}/{$total} ({$this->score_percent}%)";
    }

    /**
     * Jawaban user untuk quiz tertentu (by quiz ID).
     */
    public function getAnswerFor(int $quizId): ?string
    {
        return $this->answers[$quizId] ?? null;
    }

    /**
     * Cek apakah attempt ini mencapai passing score section.
     */
    public function meetsPassingScore(): bool
    {
        $passingScore = $this->section?->passing_score ?? 70;
        return $this->score_percent >= $passingScore;
    }

    /**
     * Hitung otomatis next attempt number untuk user + section tertentu.
     */
    public static function nextAttemptNumber(int $userId, int $sectionId): int
    {
        return static::where('user_id', $userId)
                     ->where('section_id', $sectionId)
                     ->max('attempt_number') + 1;
    }
}
