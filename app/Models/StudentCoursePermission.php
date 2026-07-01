<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCoursePermission extends Model
{
    protected $fillable = ['user_id', 'course_id', 'granted_by', 'reason', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
