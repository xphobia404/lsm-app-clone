<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
            'is_active'     => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Update kolom last_login_at ke waktu sekarang.
     * Dipanggil setelah login berhasil di LoginController.
     */
    public function touchLastLogin(): void
    {
        $this->timestamps = false; // jangan update updated_at
        $this->update(['last_login_at' => now()]);
        $this->timestamps = true;
    }

    // ── Relations ─────────────────────────────────────────────

    /**
     * Learning schemas yang diambil user ini (enrollment).
     * Akses pivot: $schema->pivot->enrolled_at, $schema->pivot->status
     */
    public function learningSchemas()
    {
        return $this->belongsToMany(LearningSchema::class, 'learning_schema_user')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps()
            ->orderByPivot('enrolled_at', 'desc');
    }

    /** Hanya schema yang statusnya active */
    public function activeLearningSchemas()
    {
        return $this->learningSchemas()->wherePivot('status', 'active');
    }

    /** Hanya schema yang sudah completed */
    public function completedLearningSchemas()
    {
        return $this->learningSchemas()->wherePivot('status', 'completed');
    }

    public function progresses()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Enrollment helpers ────────────────────────────────────

    /** Cek apakah user sudah enroll schema tertentu */
    public function isEnrolledIn(int $learningSchemaId): bool
    {
        return $this->learningSchemas()->where('learning_schema_id', $learningSchemaId)->exists();
    }

    /** Enroll user ke schema (ignore jika sudah ada) */
    public function enroll(int $learningSchemaId, string $status = 'active'): void
    {
        $this->learningSchemas()->syncWithoutDetaching([
            $learningSchemaId => ['status' => $status, 'enrolled_at' => now()],
        ]);
    }

    /** Unenroll user dari schema */
    public function unenroll(int $learningSchemaId): void
    {
        $this->learningSchemas()->detach($learningSchemaId);
    }

    /** Update status enrollment */
    public function updateEnrollmentStatus(int $learningSchemaId, string $status): void
    {
        $this->learningSchemas()->updateExistingPivot($learningSchemaId, ['status' => $status]);
    }
}
