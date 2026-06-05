<?php

namespace App\Http\Controllers;

use App\Models\SkillAssessmentQuestion;
use App\Models\SkillAssessment;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillAssessmentController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $lastAssessment = SkillAssessment::where('user_id', $user->id)->latest()->first();

        $basic        = SkillAssessmentQuestion::where('difficulty', 'basic')->inRandomOrder()->take(4)->get();
        $intermediate = SkillAssessmentQuestion::where('difficulty', 'intermediate')->inRandomOrder()->take(3)->get();
        $advanced     = SkillAssessmentQuestion::where('difficulty', 'advanced')->inRandomOrder()->take(3)->get();
        $questions    = $basic->concat($intermediate)->concat($advanced)->shuffle();

        return view('student.skill-assessment', compact('questions', 'lastAssessment'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'answers'   => 'required|array|size:10',
            'answers.*' => 'required|in:a,b,c,d',
        ]);

        $user        = Auth::user();
        $answers     = $request->input('answers', []);
        $questionIds = array_keys($answers);

        $validCount = SkillAssessmentQuestion::whereIn('id', $questionIds)->count();
        if ($validCount !== 10) {
            return redirect()->route('student.skill-assessment')
                ->with('error', 'Invalid assessment submission — please try again.');
        }

        $score     = 0;
        $total     = 10;
        $questions = SkillAssessmentQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

        foreach ($answers as $qId => $selected) {
            $q = $questions->get($qId);
            if ($q && strtolower($q->correct_option) === strtolower($selected)) {
                $score++;
            }
        }

        $level = match(true) {
            $score <= 3  => 'Basic',
            $score <= 7  => 'Intermediate',
            default      => 'Advanced',
        };

        SkillAssessment::create([
            'user_id'           => $user->id,
            'score'             => $score,
            'total_questions'   => $total,
            'recommended_level' => $level,
        ]);

        return redirect()->route('student.recommendations')
            ->with('success', "Assessment complete! You scored {$score}/{$total}. Recommended level: {$level}");
    }

    public function recommendations()
    {
        $user           = Auth::user();
        $lastAssessment = SkillAssessment::where('user_id', $user->id)->latest()->first();

        if (!$lastAssessment) {
            return redirect()->route('student.skill-assessment')
                ->with('info', 'Take the skill assessment first to get course recommendations.');
        }

        $level       = $lastAssessment->recommended_level;
        $enrolledIds = $user->enrollments()->pluck('course_id')->toArray();

        $courseLevelMap = [
            'Basic'        => 'Beginner',
            'Intermediate' => 'Intermediate',
            'Advanced'     => 'Advanced',
        ];
        $courseLevel = $courseLevelMap[$level] ?? $level;

        $recommended = Course::where('is_published', true)
            ->where('level', $courseLevel)
            ->withCount('lessons', 'enrollments')
            ->with('teacher')
            ->get();

        $otherCourses = Course::where('is_published', true)
            ->where('level', '!=', $courseLevel)
            ->withCount('lessons', 'enrollments')
            ->with('teacher')
            ->get();

        return view('student.recommendations', compact(
            'lastAssessment', 'recommended', 'otherCourses', 'enrolledIds', 'level'
        ));
    }
}
