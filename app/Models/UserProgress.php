<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeUnlocked($query)
    {
        return $query->where('unlocked', true);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

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
     * Tandai section sebagai mulai dibaca.
     */
    public function markInProgress(): void
    {
        if ($this->status === 'not_started') {
            $this->update(['status' => 'in_progress']);
        }
    }

    /**
     * Tandai section sebagai selesai dibaca.
     */
    public function markCompleted(): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => $this->completed_at ?? now(),
        ]);
    }

    /**
     * Tandai quiz section ini sudah lulus.
     */
    public function markQuizPassed(): void
    {
        $this->update([
            'quiz_passed_at' => $this->quiz_passed_at ?? now(),
        ]);
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
     * Badge color untuk UI (Tailwind / Vuexy).
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
