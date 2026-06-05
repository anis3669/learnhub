<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillAssessment extends Model
{
    protected $fillable = ['user_id', 'score', 'total_questions', 'recommended_level'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getScorePercentAttribute(): float
    {
        return $this->total_questions > 0
            ? round(($this->score / $this->total_questions) * 100, 1)
            : 0;
    }
}
