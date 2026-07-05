<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'section_id',
        'status',
        'unlocked',
        'completed_at',
        'quiz_passed_at',
    ];

    protected $casts = [
        'unlocked'       => 'boolean',
        'completed_at'   => 'datetime',
        'quiz_passed_at' => 'datetime',
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

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isNotStarted(): bool
    {
        return $this->status === 'not_started';
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed'   => 'Selesai',
            'in_progress' => 'Sedang Dipelajari',
            default       => 'Belum Dimulai',
        };
    }

    /**
     * Badge color untuk UI (Tailwind / Bootstrap class).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed'   => 'success',
            'in_progress' => 'warning',
            default       => 'secondary',
        };
    }
}
