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
        'learning_schema_id',
        'section_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'started_at'          => 'datetime',
            'completed_at'        => 'datetime',
        ];
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learningSchema()
    {
        return $this->belongsTo(LearningSchema::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Helpers
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isStarted(): bool
    {
        return in_array($this->status, ['in_progress', 'completed']);
    }
}
