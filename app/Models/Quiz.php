<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'order',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Semua media milik soal ini (polymorphic).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('order');
    }

    /**
     * Shortcut: hanya gambar.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
                    ->where('type', 'image')
                    ->orderBy('order');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Cek apakah jawaban user benar.
     */
    public function checkAnswer(string $answer): bool
    {
        return strtolower(trim($answer)) === strtolower(trim($this->correct_answer));
    }

    /**
     * Semua pilihan dalam format array asosiatif.
     * Hanya return option yang tidak null.
     */
    public function getOptions(): array
    {
        return array_filter([
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ], fn ($v) => ! is_null($v));
    }

    /**
     * Shortcut akses course type via section.
     */
    public function getCourseTypeAttribute(): ?CourseType
    {
        return $this->section?->courseType;
    }
}
