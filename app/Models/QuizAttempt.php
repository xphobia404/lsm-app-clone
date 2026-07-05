<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'answers'        => 'array',   // auto encode/decode JSON
        'submitted_at'   => 'datetime',
    ];

    // -------------------------
    // Relations
    // -------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // -------------------------
    // Helpers
    // -------------------------

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
     * Jawaban user untuk quiz tertentu.
     */
    public function getAnswerFor(int $quizId): ?string
    {
        return $this->answers[$quizId] ?? null;
    }
}
