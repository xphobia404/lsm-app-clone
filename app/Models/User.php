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
        'password',
        'role',
        'is_active',
        'course_type_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────

    /** Many-to-many: user bisa punya banyak spesialisasi */
    public function courseTypes()
    {
        return $this->belongsToMany(CourseType::class, 'course_type_user')
                    ->withTimestamps();
    }

    /** Legacy single spesialisasi (backward compat) */
    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    /** User progress (alias: progress & progresses keduanya valid) */
    public function progresses()
    {
        return $this->hasMany(UserProgress::class);
    }

    /** Alias agar $user->progress juga bekerja */
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasSelectedCourseType(): bool
    {
        return $this->courseTypes()->exists();
    }

    public function hasCourseType(int $courseTypeId): bool
    {
        return $this->courseTypes()->where('course_type_id', $courseTypeId)->exists();
    }
}
