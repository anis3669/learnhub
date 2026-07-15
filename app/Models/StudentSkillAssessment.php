<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSkillAssessment extends Model
{
    protected $fillable = [
        'user_id', 'familiar_with_programming', 'score',
        'total_questions', 'correct_answers', 'completed',
    ];

    protected $casts = [
        'familiar_with_programming' => 'boolean',
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getScorePercentAttribute(): int
    {
        if (! $this->total_questions) {
            return 0;
        }

        return (int) round(($this->correct_answers / $this->total_questions) * 100);
    }

    public function isHighScore(): bool
    {
        return $this->score_percent >= 80;
    }
}
