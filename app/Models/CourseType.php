<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // Auto-generate slug from name
    protected static function booted(): void
    {
        static::creating(function (CourseType $ct) {
            if (empty($ct->slug)) {
                $ct->slug = Str::slug($ct->name) . '-' . time();
            }
        });
    }

    // Relations
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // Many-to-many via pivot course_type_user
    public function users()
    {
        return $this->belongsToMany(User::class, 'course_type_user')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
