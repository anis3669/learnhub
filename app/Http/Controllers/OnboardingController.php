<?php

namespace App\Http\Controllers;

use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function __construct(private LearningPathService $learningPathService) {}

    public function assessment()
    {
        $user = Auth::user();
        $assessment = \App\Models\StudentSkillAssessment::where('user_id', $user->id)->first();
        if ($assessment && $assessment->completed) {
            return redirect()->route('student.dashboard');
        }
        $questions = $this->learningPathService->getAssessmentQuestions();
        return view('student.onboarding', compact('questions'));
    }

    public function familiar(Request $request)
    {
        $user = Auth::user();
        $familiar = $request->boolean('familiar');

        if (!$familiar) {
            $this->learningPathService->processFamiliarityResponse($user, false);
            return redirect()->route('student.dashboard')
                ->with('success', 'Welcome! We\'ve set up your Introduction learning path. 🚀');
        }

        return redirect()->route('student.assessment');
    }

    public function submitAssessment(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $questions = $this->learningPathService->getAssessmentQuestions();
        $correctAnswers = 0;

        foreach ($questions as $q) {
            $submitted = $request->input("answers.{$q['id']}");
            if ($submitted !== null && (int) $submitted === $q['correct']) {
                $correctAnswers++;
            }
        }

        $assessment = $this->learningPathService->processAssessmentResult(
            Auth::user(),
            true,
            $correctAnswers
        );

        $score = $assessment->score_percent;
        if ($score >= 80) {
            $msg = "Great score ({$score}%)! You've been placed on the Advanced Track — Beginner & Intermediate courses are unlocked. 🎓";
        } else {
            $msg = "Score: {$score}%. We recommend starting with Introduction courses and progressing from there. 📚";
        }

        return redirect()->route('student.dashboard')->with('success', $msg);
    }
}
