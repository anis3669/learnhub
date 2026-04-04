<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
>>>>>>> anis

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'course_id', 'title', 'description', 'video_url', 'content',
        'duration_minutes', 'order', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function getEmbedUrlAttribute()
    {
        $url = $this->video_url;
        if (!$url) return null;

        if (str_contains($url, 'youtube.com/watch?v=')) {
            $id = explode('v=', $url)[1];
            $id = explode('&', $id)[0];
            return "https://www.youtube.com/embed/{$id}";
        }
        if (str_contains($url, 'youtu.be/')) {
            $id = substr($url, strrpos($url, '/') + 1);
            return "https://www.youtube.com/embed/{$id}";
        }
        if (str_contains($url, 'vimeo.com/')) {
            $id = substr($url, strrpos($url, '/') + 1);
            return "https://player.vimeo.com/video/{$id}";
        }
        return $url;
    }
=======
        'course_id',
        'title',
        'video',
        'duration',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
>>>>>>> anis
}
