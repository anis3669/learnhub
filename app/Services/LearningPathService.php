<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentCoursePermission;
use App\Models\StudentLearningPath;
use App\Models\StudentSkillAssessment;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LearningPathService
{
    public function getAssessmentQuestions(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'What does HTML stand for?',
                'options' => ['Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Layout', 'Home Tool Markup Language'],
                'correct' => 0,
            ],
            [
                'id' => 2,
                'text' => 'Which of the following is a programming language?',
                'options' => ['Microsoft Word', 'Python', 'Google Chrome', 'Adobe Photoshop'],
                'correct' => 1,
            ],
            [
                'id' => 3,
                'text' => 'What is a variable in programming?',
                'options' => ['A fixed value that never changes', 'A storage location with a name that holds a value', 'A type of loop', 'A function argument'],
                'correct' => 1,
            ],
            [
                'id' => 4,
                'text' => 'What is the result of 10 % 3 in most programming languages?',
                'options' => ['3', '0', '1', '3.33'],
                'correct' => 2,
            ],
            [
                'id' => 5,
                'text' => 'Which keyword is used to define a function in Python?',
                'options' => ['function', 'func', 'def', 'define'],
                'correct' => 2,
            ],
            [
                'id' => 6,
                'text' => 'What does "debugging" mean in programming?',
                'options' => ['Writing new code features', 'Finding and fixing errors in code', 'Deleting unused files', 'Compressing code size'],
                'correct' => 1,
            ],
            [
                'id' => 7,
                'text' => 'What is an array (or list) in programming?',
                'options' => ['A single value container', 'An ordered collection of elements', 'A mathematical formula', 'A type of database'],
                'correct' => 1,
            ],
            [
                'id' => 8,
                'text' => 'What does API stand for?',
                'options' => ['Advanced Programming Interface', 'Application Protocol Integration', 'Application Programming Interface', 'Automated Process Integration'],
                'correct' => 2,
            ],
            [
                'id' => 9,
                'text' => 'Which of these is a loop structure in programming?',
                'options' => ['if-else', 'switch', 'for', 'class'],
                'correct' => 2,
            ],
            [
                'id' => 10,
                'text' => 'What does OOP stand for?',
                'options' => ['Open Operational Programming', 'Object-Oriented Programming', 'Ordered Output Processing', 'Optimal Output Programming'],
                'correct' => 1,
            ],
        ];
    }

    public function processAssessmentResult(User $user, bool $familiarWithProgramming, int $correctAnswers): StudentSkillAssessment
    {
        $totalQuestions = 10;
        $score = (int) round(($correctAnswers / $totalQuestions) * 100);

        $assessment = StudentSkillAssessment::updateOrCreate(
            ['user_id' => $user->id],
            [
                'familiar_with_programming' => $familiarWithProgramming,
                'score' => $familiarWithProgramming ? $score : null,
                'total_questions' => $totalQuestions,
                'correct_answers' => $familiarWithProgramming ? $correctAnswers : 0,
                'completed' => true,
            ]
        );

        $this->createOrUpdateLearningPath($user, $familiarWithProgramming, $score);

        return $assessment;
    }

    public function processFamiliarityResponse(User $user, bool $familiarWithProgramming): void
    {
        if (! $familiarWithProgramming) {
            StudentSkillAssessment::updateOrCreate(
                ['user_id' => $user->id],
                ['familiar_with_programming' => false, 'completed' => true]
            );
            $this->createOrUpdateLearningPath($user, false, 0);
        }
    }

    private function createOrUpdateLearningPath(User $user, bool $familiarWithProgramming, int $score): void
    {
        if (! $familiarWithProgramming) {
            $pathType = StudentLearningPath::PATH_NO_CODING;
            $unlockedLevel = StudentLearningPath::LEVEL_INTRODUCTION;
        } elseif ($score >= 80) {
            $pathType = StudentLearningPath::PATH_HIGH_SCORE;
            $unlockedLevel = StudentLearningPath::LEVEL_INTERMEDIATE;
        } else {
            $pathType = StudentLearningPath::PATH_LOW_SCORE;
            $unlockedLevel = StudentLearningPath::LEVEL_INTRODUCTION;
        }

        StudentLearningPath::updateOrCreate(
            ['user_id' => $user->id],
            ['path_type' => $pathType, 'unlocked_level' => $unlockedLevel]
        );
    }

    public function canAccessCourse(User $user, Course $course): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('teacher')) {
            return true;
        }

        $permission = StudentCoursePermission::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->first();
        if ($permission) {
            return true;
        }

        $learningPath = StudentLearningPath::where('user_id', $user->id)->first();
        if (! $learningPath) {
            return true;
        }

        return $learningPath->canAccessLevel($course->level);
    }

    public function getRecommendedCourses(User $user): Collection
    {
        $learningPath = StudentLearningPath::where('user_id', $user->id)->first();
        if (! $learningPath) {
            return Course::where('is_published', true)->where('level', 'Beginner')->take(3)->get();
        }

        $enrolledIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

        return Course::where('is_published', true)
            ->where('level', $learningPath->unlocked_level)
            ->whereNotIn('id', $enrolledIds)
            ->take(3)
            ->get();
    }

    public function checkAndUnlockNextLevel(User $user): void
    {
        $learningPath = StudentLearningPath::where('user_id', $user->id)->first();
        if (! $learningPath) {
            return;
        }

        $levelRanks = StudentLearningPath::LEVEL_RANKS;
        $currentRank = $levelRanks[$learningPath->unlocked_level] ?? 1;

        $nextLevel = null;
        foreach ($levelRanks as $level => $rank) {
            if ($rank === $currentRank + 1) {
                $nextLevel = $level;
                break;
            }
        }

        if (! $nextLevel) {
            return;
        }

        $completedInCurrentLevel = Enrollment::where('user_id', $user->id)
            ->whereHas('course', fn ($q) => $q->where('level', $learningPath->unlocked_level))
            ->where(function ($q) {
                $q->where('progress_percent', '>=', 100)->orWhereNotNull('completed_at');
            })
            ->count();

        if ($completedInCurrentLevel > 0) {
            $learningPath->update(['unlocked_level' => $nextLevel]);
        }
    }

    public function getLearningPathProgress(User $user): array
    {
        $learningPath = StudentLearningPath::where('user_id', $user->id)->first();
        if (! $learningPath) {
            return [];
        }

        $levels = ['Introduction', 'Beginner', 'Intermediate', 'Advanced'];
        $current = StudentLearningPath::LEVEL_RANKS[$learningPath->unlocked_level] ?? 1;
        $progress = [];
        foreach ($levels as $level) {
            $rank = StudentLearningPath::LEVEL_RANKS[$level];
            $completed = Enrollment::where('user_id', $user->id)
                ->whereHas('course', fn ($q) => $q->where('level', $level))
                ->where('progress_percent', '>=', 100)
                ->count();
            $total = Course::where('level', $level)->where('is_published', true)->count();
            $progress[] = [
                'level' => $level,
                'rank' => $rank,
                'unlocked' => $rank <= $current,
                'completed' => $completed,
                'total' => $total,
            ];
        }

        return $progress;
    }

    public function grantCourseAccess(User $student, Course $course, User $admin, ?string $reason = null): void
    {
        StudentCoursePermission::updateOrCreate(
            ['user_id' => $student->id, 'course_id' => $course->id],
            ['granted_by' => $admin->id, 'reason' => $reason, 'is_active' => true]
        );
    }

    public function revokeCourseAccess(User $student, Course $course): void
    {
        StudentCoursePermission::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->update(['is_active' => false]);
    }

    public function getDailyStreak(User $user): int
    {
        $dates = UserProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereNotNull('completed_at')
            ->selectRaw('DATE(completed_at) as day')
            ->distinct()
            ->orderByDesc('day')
            ->pluck('day')
            ->toArray();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $check = now()->startOfDay();

        foreach ($dates as $day) {
            $d = Carbon::parse($day)->startOfDay();
            if ($d->equalTo($check) || $d->equalTo($check->copy()->subDay())) {
                $streak++;
                $check = $d->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    public function getUpcomingQuizzes(User $user): Collection
    {
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)->pluck('course_id');
        $attemptedQuizIds = QuizAttempt::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('quiz_id');

        return Quiz::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->whereNotIn('id', $attemptedQuizIds)
            ->where('is_published', true)
            ->take(5)
            ->get();
    }
}
