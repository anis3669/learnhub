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

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'teacher_id', 'title', 'description', 'thumbnail', 'category',
        'level', 'is_published', 'duration_hours',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function teacher()
=======
        'title',
        'description',
        'thumbnail',
        'teacher_id',
    ];

    public function teacher(): BelongsTo
>>>>>>> anis
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

<<<<<<< HEAD
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')->withPivot('progress_percent', 'completed_at')->withTimestamps();
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function discussions()
    {
        return $this->hasMany(DiscussionPost::class)->latest();
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        $colors = ['6366f1', 'ec4899', 'f59e0b', '10b981', '3b82f6', 'ef4444'];
        $color = $colors[$this->id % count($colors)];
        return "https://via.placeholder.com/800x450/{$color}/ffffff?text=" . urlencode($this->title);
    }
=======
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }
>>>>>>> anis
}
