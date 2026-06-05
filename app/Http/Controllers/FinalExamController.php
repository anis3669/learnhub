<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuestionBank;
use App\Models\FinalExamAttempt;
use App\Models\FinalExamAnswer;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinalExamController extends Controller
{
    public function show(Course $course)
    {
        $user = Auth::user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.courses')
                ->with('error', 'You must be enrolled in this course to take the final exam.');
        }

        if (!$enrollment->completed_at) {
            return redirect()->route('student.course.show', $course)
                ->with('error', 'You must complete all lessons before taking the final exam.');
        }

        $passedAttempt = FinalExamAttempt::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('passed', true)
            ->first();

        if ($passedAttempt) {
            return redirect()->route('student.final-exam.result', [$course, $passedAttempt])
                ->with('info', 'You have already passed this final exam.');
        }

        $bankCount = QuestionBank::where('course_id', $course->id)->count();
        if ($bankCount < 20) {
            return redirect()->route('student.course.show', $course)
                ->with('error', 'The final exam is not ready yet. Please check back later.');
        }

        $questions = QuestionBank::where('course_id', $course->id)
            ->inRandomOrder()
            ->take(20)
            ->get();

        $attempt = FinalExamAttempt::create([
            'user_id'        => $user->id,
            'course_id'      => $course->id,
            'score'          => 0,
            'total_questions'=> 20,
            'passed'         => false,
            'started_at'     => now(),
        ]);

        foreach ($questions as $q) {
            FinalExamAnswer::create([
                'attempt_id'      => $attempt->id,
                'bank_question_id'=> $q->id,
                'selected_option' => null,
                'is_correct'      => false,
            ]);
        }

        $attempt->load('answers.question');

        return view('student.final-exam', compact('course', 'attempt', 'questions'));
    }

    public function submit(Request $request, Course $course, FinalExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        if ($attempt->completed_at) {
            return redirect()->route('student.final-exam.result', [$course, $attempt]);
        }

        $answers   = $request->input('answers', []);
        $score     = 0;
        $questions = QuestionBank::whereIn('id', array_keys($answers))->get()->keyBy('id');

        foreach ($attempt->answers as $answer) {
            $qId     = $answer->bank_question_id;
            $selected = isset($answers[$qId]) ? (int)$answers[$qId] : null;
            $q        = $questions->get($qId);
            $correct  = $q && $selected === $q->correct_option;

            if ($correct) $score++;

            $answer->update([
                'selected_option' => $selected,
                'is_correct'      => $correct,
            ]);
        }

        $passed = $score >= 15;

        $attempt->update([
            'score'        => $score,
            'passed'       => $passed,
            'completed_at' => now(),
        ]);

        if ($passed) {
            $user->increment('points', 100);
            $this->awardCompletionBadge($user, $course);
        }

        return redirect()->route('student.final-exam.result', [$course, $attempt]);
    }

    public function result(Course $course, FinalExamAttempt $attempt)
    {
        $user = Auth::user();
        if ($attempt->user_id !== $user->id) abort(403);

        $attempt->load('answers.question');

        $allAttempts = FinalExamAttempt::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->latest()
            ->get();

        return view('student.final-exam-result', compact('course', 'attempt', 'allAttempts'));
    }

    private function awardCompletionBadge(object $user, Course $course): void
    {
        $user->refresh();
        $earned = $user->badges()->pluck('badges.id')->toArray();
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if (in_array($badge->id, $earned)) continue;
            $award = match($badge->criteria_type) {
                'points'           => $user->points >= $badge->criteria_value,
                'lessons_completed'=> \App\Models\UserProgress::where('user_id', $user->id)->where('is_completed', true)->count() >= $badge->criteria_value,
                'courses_completed'=> Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count() >= $badge->criteria_value,
                'enrollments'      => Enrollment::where('user_id', $user->id)->count() >= $badge->criteria_value,
                default            => false,
            };
            if ($award) {
                DB::table('user_badges')->insertOrIgnore([
                    'user_id'    => $user->id,
                    'badge_id'   => $badge->id,
                    'earned_at'  => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
