<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExamAnswer extends Model
{
    protected $fillable = ['attempt_id', 'bank_question_id', 'selected_option', 'is_correct'];

    protected $casts = ['is_correct' => 'boolean'];

    public function attempt()
    {
        return $this->belongsTo(FinalExamAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'bank_question_id');
    }
}
