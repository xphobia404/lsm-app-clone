<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Many-to-many: user bisa punya banyak course type (via pivot course_type_user).
     */
    public function courseTypes(): BelongsToMany
    {
        return $this->belongsToMany(CourseType::class, 'course_type_user')
                    ->withTimestamps();
    }

    /**
     * Riwayat progress belajar user per section.
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Alias $user->progress juga bekerja.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Semua percobaan quiz yang dilakukan user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Cek apakah user sudah assign ke minimal 1 course type.
     */
    public function hasSelectedCourseType(): bool
    {
        return $this->courseTypes()->exists();
    }

    /**
     * Cek apakah user punya course type tertentu.
     */
    public function hasCourseType(int $courseTypeId): bool
    {
        return $this->courseTypes()->where('course_type_id', $courseTypeId)->exists();
    }

    /**
     * Tandai waktu login terakhir.
     */
    public function touchLastLogin(): void
    {
        $this->forceFill(['last_login_at' => now()])->save();
    }

    /**
     * Progress user untuk section tertentu (helper shortcut).
     */
    public function progressForSection(int $sectionId): ?UserProgress
    {
        return $this->progresses()->where('section_id', $sectionId)->first();
    }

    /**
     * Attempt terakhir user untuk section tertentu.
     */
    public function latestAttemptFor(int $sectionId): ?QuizAttempt
    {
        return $this->quizAttempts()
                    ->where('section_id', $sectionId)
                    ->latest('attempt_number')
                    ->first();
    }
}
