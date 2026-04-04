<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'points', 'avatar', 'bio',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

<<<<<<< HEAD
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
=======
    // ==================== RELATIONSHIPS ====================

    /**
     * Courses created by this teacher
     */
    public function coursesAsTeacher(): HasMany
>>>>>>> anis
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

<<<<<<< HEAD
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withPivot('earned_at')->withTimestamps();
    }

    public function discussionPosts()
    {
        return $this->hasMany(DiscussionPost::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')->withPivot('progress_percent', 'completed_at')->withTimestamps();
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff&size=128";
    }

    public function getBestQuizScore()
    {
        return $this->quizAttempts()->max('score') ?? 0;
    }
=======
    /**
     * You can add more relationships later (e.g. enrolled courses for students)
     */
    // public function enrolledCourses()
    // {
    //     return $this->belongsToMany(Course::class, 'enrollments');
    // }
>>>>>>> anis
}
