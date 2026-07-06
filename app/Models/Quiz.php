<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // -------------------------
    // Relations
    // -------------------------

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Semua media milik soal ini (polymorphic).
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('order');
    }

    /**
     * Shortcut: hanya gambar.
     */
    public function images()
    {
        return $this->morphMany(Media::class, 'mediable')
                    ->where('type', 'image')
                    ->orderBy('order');
    }

    // -------------------------
    // Helpers
    // -------------------------

    public function checkAnswer(string $answer): bool
    {
        return strtolower(trim($answer)) === strtolower(trim($this->correct_answer));
    }

    public function getOptions(): array
    {
        return [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ];
    }
}
