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
        'question_image',
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

    // -------------------------
    // Helpers
    // -------------------------

    /**
     * Cek apakah jawaban user benar.
     */
    public function checkAnswer(string $answer): bool
    {
        return strtolower(trim($answer)) === strtolower(trim($this->correct_answer));
    }

    /**
     * Ambil semua opsi dalam format key => label.
     * Berguna untuk looping di Blade/Vue.
     */
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
