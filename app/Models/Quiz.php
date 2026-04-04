<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
>>>>>>> anis

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'course_id', 'lesson_id', 'title', 'description',
        'time_limit_minutes', 'passing_score', 'is_published',
    ];

    protected $casts = ['is_published' => 'boolean'];

    public function course()
=======
        'course_id',
        'title',
        'description',
    ];

    public function course(): BelongsTo
>>>>>>> anis
    {
        return $this->belongsTo(Course::class);
    }

<<<<<<< HEAD
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
=======
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
>>>>>>> anis
    }
}
