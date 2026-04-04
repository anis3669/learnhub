<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionPost extends Model
{
    protected $fillable = ['user_id', 'course_id', 'title', 'body', 'likes', 'is_pinned'];

    protected $casts = ['is_pinned' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class, 'post_id')->latest();
    }
}
