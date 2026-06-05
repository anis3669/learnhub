<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExamAttempt extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'score', 'total_questions',
        'passed', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'passed'       => 'boolean',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function answers()
    {
        return $this->hasMany(FinalExamAnswer::class, 'attempt_id');
    }

    public function getScorePercentAttribute(): float
    {
        return $this->total_questions > 0
            ? round(($this->score / $this->total_questions) * 100, 1)
            : 0;
    }
}
