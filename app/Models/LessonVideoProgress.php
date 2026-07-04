<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonVideoProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lesson_id', 'watched_seconds', 'duration_seconds',
        'watch_percentage', 'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'watched_seconds' => 'integer',
        'duration_seconds' => 'integer',
        'watch_percentage' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
