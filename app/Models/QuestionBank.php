<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $table = 'question_bank';

    protected $fillable = [
        'course_id', 'question_text',
        'option_a', 'option_b', 'option_c', 'option_d',
        'correct_option', 'difficulty',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function finalExamAnswers()
    {
        return $this->hasMany(FinalExamAnswer::class, 'bank_question_id');
    }
}
