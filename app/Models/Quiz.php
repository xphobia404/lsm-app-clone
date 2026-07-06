<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected $casts = [
        'is_active'   => 'boolean',
        'quiz_order'  => 'integer',
    ];

    // ── Relationships ───────────────────────────────────────────────

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Media untuk soal quiz (misal: gambar soal, diagram, ilustrasi).
     * Bisa dipakai: $quiz->media, $quiz->media()->ofType('image')->get()
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->orderBy('media_order');
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('quiz_order');
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function isCorrect(string $answer): bool
    {
        return strtolower($answer) === strtolower($this->correct_answer);
    }

    /**
     * Kembalikan semua opsi yang terisi sebagai array ['a' => '...', 'b' => '...'].
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

    /**
     * Kembalikan teks jawaban yang benar.
     */
    public function getCorrectAnswerText(): string
    {
        return $this->{'option_' . $this->correct_answer} ?? '';
    }

    /**
     * Apakah quiz ini punya gambar soal?
     */
    public function hasMedia(): bool
    {
        return $this->media()->exists();
    }
}
