<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model
{
    protected $fillable = ['post_id', 'user_id', 'body', 'likes'];

    public function post()
    {
        return $this->belongsTo(DiscussionPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
