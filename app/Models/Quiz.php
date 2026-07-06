<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Quiz extends Model
{
    protected $fillable = [
        'section_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
        'quiz_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'quiz_order' => 'integer',
        ];
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('quiz_order');
    }

    // ── Relations ────────────────────────────────────────────────────────

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Media yang melekat pada soal quiz ini (gambar/video/audio/url).
     * Diurutkan berdasarkan media_order.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->orderBy('media_order');
    }

    public function activeMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('is_active', true)
            ->orderBy('media_order');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function isCorrect(string $answer): bool
    {
        return strtolower($answer) === strtolower($this->correct_answer);
    }

    /**
     * Kembalikan array opsi yang diisi (tidak null).
     * Contoh: ['a' => 'Benar', 'b' => 'Salah', 'c' => 'Mungkin']
     */
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
