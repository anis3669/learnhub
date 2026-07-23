<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use App\Models\Badge;
use App\Models\Course;
use App\Models\CoursePayment;
use App\Models\DiscussionPost;
use App\Models\DiscussionReply;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonVideoProgress;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentLearningPath;
use App\Models\StudentSkillAssessment;
use App\Models\User;
use App\Models\UserProgress;
use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function __construct(private LearningPathService $learningPathService) {}

    public function dashboard()
    {
        $user = Auth::user();
        $enrollments = Enrollment::with('course.teacher', 'course.lessons')
            ->where('user_id', $user->id)->latest()->get();

        $recentAttempts = QuizAttempt::with('quiz.course')
            ->where('user_id', $user->id)->whereNotNull('completed_at')->latest()->take(5)->get();

        $badges = $user->badges()->latest('user_badges.created_at')->get();
        $leaderboard = $this->getLeaderboardTop(5);
        $rank = $this->getUserRank($user->id);

        $assessment = StudentSkillAssessment::where('user_id', $user->id)->first();
        $showOnboarding = ! $assessment || ! $assessment->completed;

        $learningPath = StudentLearningPath::where('user_id', $user->id)->first();
        $learningPathProgress = $this->learningPathService->getLearningPathProgress($user);
        $recommendedCourses = $this->learningPathService->getRecommendedCourses($user);
        $streak = $this->learningPathService->getDailyStreak($user);
        $upcomingQuizzes = $this->learningPathService->getUpcomingQuizzes($user);

        $enrolledCourseIds = $enrollments->pluck('course.id');
        $recentDiscussions = DiscussionPost::with('user', 'course', 'replies')
            ->whereIn('course_id', $enrolledCourseIds)->latest()->take(4)->get();

        $continueLearning = $enrollments->filter(fn ($e) => $e->progress_percent > 0 && $e->progress_percent < 100)->first();

        return view('student.dashboard', compact(
            'user', 'enrollments', 'recentAttempts', 'badges', 'leaderboard', 'rank',
            'showOnboarding', 'learningPath', 'learningPathProgress', 'recommendedCourses',
            'streak', 'upcomingQuizzes', 'recentDiscussions', 'continueLearning'
        ));
    }

    public function courses(Request $request)
    {
        $user = Auth::user();
        $query = Course::with('teacher')->where('is_published', true);
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        $courses = $query->withCount('enrollments', 'lessons')->latest()->paginate(12);
        $enrolledIds = $user->enrollments()->pluck('course_id')->toArray();
        $categories = Course::where('is_published', true)->distinct()->pluck('category');

        $courseAccess = [];
        foreach ($courses as $course) {
            $courseAccess[$course->id] = $this->learningPathService->canAccessCourse($user, $course);
        }

        return view('student.courses', compact('courses', 'enrolledIds', 'categories', 'courseAccess'));
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();

        if ($course->requiresPayment()) {
            return redirect()->route('student.course.payment.initiate', $course);
        }

        if (! $this->learningPathService->canAccessCourse($user, $course)) {
            return redirect()->back()->with('error', 'Complete the prerequisite courses to unlock this level.');
        }

        Enrollment::firstOrCreate(['user_id' => $user->id, 'course_id' => $course->id], ['progress_percent' => 0]);

        return redirect()->route('student.course.show', $course)->with('success', "Enrolled in \"{$course->title}\" successfully!");
    }

    public function showCourse(Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();

        if ($course->requiresPayment()) {
            $paid = CoursePayment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'completed')
                ->exists();

            if (! $enrollment || ! $paid) {
                return redirect()->route('student.courses')
                    ->with('error', 'Please complete the payment to access this premium course.');
            }
        }

        if (! $enrollment) {
            return redirect()->route('student.courses')->with('error', 'Please enroll in the course first.');
        }
        $lessons = $course->lessons()->with(['progress' => fn ($q) => $q->where('user_id', $user->id)])->get();
        $quizzes = $course->quizzes()->where('is_published', true)->with(['attempts' => fn ($q) => $q->where('user_id', $user->id)])->get();
        $posts = DiscussionPost::with('user', 'replies')->where('course_id', $course->id)->latest()->take(5)->get();
        $completedCount = $lessons->filter(fn ($l) => $l->progress->isNotEmpty() && $l->progress->first()->is_completed)->count();
        $progress = $lessons->count() > 0 ? round(($completedCount / $lessons->count()) * 100) : 0;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->update(['progress_percent' => $progress]);

        return view('student.course-show', compact('course', 'enrollment', 'lessons', 'quizzes', 'posts', 'progress'));
    }

    public function watchLesson(Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        if ($lesson->course_id !== $course->id) {
            abort(403);
        }

        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (! $enrollment) {
            return redirect()->route('student.courses');
        }
        $prev = $course->lessons()->where('order', '<', $lesson->order)->orderByDesc('order')->first();
        $next = $course->lessons()->where('order', '>', $lesson->order)->orderBy('order')->first();
        $userProgress = UserProgress::firstOrCreate(['user_id' => $user->id, 'lesson_id' => $lesson->id], ['watch_percent' => 0]);
        $videoProgress = LessonVideoProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();
        $quiz = $lesson->quiz()->where('is_published', true)->first();

        return view('student.lesson', compact('course', 'lesson', 'prev', 'next', 'userProgress', 'quiz', 'videoProgress'));
    }

    public function markComplete(Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        if ($lesson->course_id !== $course->id) {
            abort(403);
        }

        $videoProgress = LessonVideoProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if (! $videoProgress || $videoProgress->watch_percentage < 80) {
            return redirect()->back()->with('error', 'You must watch at least 80% of the video before marking the lesson as complete.');
        }

        UserProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => true, 'watch_percent' => 100, 'completed_at' => now()]
        );
        $user->increment('points', 20);
        $this->checkAndAwardBadges($user);

        $totalLessons = $course->lessons()->count();
        $completedLessons = UserProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)->count();
        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->update(['progress_percent' => $progress]);

        if ($progress >= 100) {
            Enrollment::where('user_id', $user->id)->where('course_id', $course->id)
                ->whereNull('completed_at')->update(['completed_at' => now()]);
        }

        $this->learningPathService->checkAndUnlockNextLevel($user);

        $next = $course->lessons()->where('order', '>', $lesson->order)->orderBy('order')->first();
        if ($next) {
            return redirect()->route('student.lesson', [$course, $next])->with('success', 'Lesson completed! +20 points ?');
        }

        return redirect()->route('student.course.show', $course)->with('success', 'Course progress updated! +20 points ?');
    }

    public function saveVideoProgress(Request $request, Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(403);
        }

        $request->validate([
            'watched_seconds' => 'required|integer|min:0',
            'duration_seconds' => 'required|integer|min:1',
            'watch_percentage' => 'required|integer|min:0|max:100',
        ]);
        $user = Auth::user();
        $duration = (int) $request->duration_seconds;
        $percentage = (int) $request->watch_percentage;
        $watched = (int) $request->watched_seconds;

        $progress = LessonVideoProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if ($progress) {
            $progress->watched_seconds = max($progress->watched_seconds, $watched);
            $progress->duration_seconds = max($progress->duration_seconds, $duration);
            $progress->watch_percentage = max($progress->watch_percentage, min(100, $percentage));
            $progress->completed = $progress->watch_percentage >= 80;
            $progress->save();
        } else {
            $progress = LessonVideoProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'watched_seconds' => $watched,
                'duration_seconds' => $duration,
                'watch_percentage' => min(100, $percentage),
                'completed' => $percentage >= 80,
            ]);
        }

        return response()->json([
            'success' => true,
            'watch_percentage' => $progress->watch_percentage,
            'completed' => $progress->completed,
            'watched_seconds' => $progress->watched_seconds,
        ]);
    }

    public function getVideoProgress(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(403);
        }

        $user = Auth::user();
        $progress = LessonVideoProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if (! $progress) {
            return response()->json([
                'watched_seconds' => 0,
                'duration_seconds' => 0,
                'watch_percentage' => 0,
                'completed' => false,
            ]);
        }

        return response()->json([
            'watched_seconds' => $progress->watched_seconds,
            'duration_seconds' => $progress->duration_seconds,
            'watch_percentage' => $progress->watch_percentage,
            'completed' => $progress->completed,
        ]);
    }

    public function takeQuiz(Course $course, Quiz $quiz)
    {
        $user = Auth::user();

        if ($quiz->course_id !== $course->id) {
            abort(403);
        }

        $questions = $quiz->questions()->with('options')->get();
        if ($questions->isEmpty()) {
            return redirect()->back()->with('error', 'This quiz has no questions yet.');
        }
        $attempt = QuizAttempt::create(['user_id' => $user->id, 'quiz_id' => $quiz->id, 'started_at' => now()]);

        return view('student.quiz', compact('course', 'quiz', 'questions', 'attempt'));
    }

    public function submitQuiz(Request $request, Course $course, Quiz $quiz)
    {
        $user = Auth::user();

        if ($quiz->course_id !== $course->id) {
            abort(403);
        }

        $attempt = QuizAttempt::where('id', $request->attempt_id)->where('user_id', $user->id)->firstOrFail();
        $questions = $quiz->questions()->with('options')->get();
        $score = 0;
        $totalPoints = $questions->sum('points');
        foreach ($questions as $question) {
            $selectedId = $request->input("answers.{$question->id}");
            $isCorrect = false;
            if ($selectedId) {
                $option = QuestionOption::find($selectedId);
                $isCorrect = $option && $option->is_correct;
                if ($isCorrect) {
                    $score += $question->points;
                }
            }
            AttemptAnswer::create(['attempt_id' => $attempt->id, 'question_id' => $question->id, 'selected_option_id' => $selectedId ?: null, 'is_correct' => $isCorrect]);
        }
        $percent = $totalPoints > 0 ? round(($score / $totalPoints) * 100) : 0;
        $passed = $percent >= $quiz->passing_score;
        $attempt->update(['score' => $score, 'total_points' => $totalPoints, 'passed' => $passed, 'completed_at' => now()]);
        $pointsEarned = $passed ? 50 : 10;
        $user->increment('points', $pointsEarned);
        $this->checkAndAwardBadges($user);

        return redirect()->route('student.quiz.result', [$course, $quiz, $attempt]);
    }

    public function quizResult(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }
        $answers = $attempt->answers()->with('question.options', 'selectedOption')->get();

        return view('student.quiz-result', compact('course', 'quiz', 'attempt', 'answers'));
    }

    public function leaderboard()
    {
        $users = User::role('student')->select('id', 'name', 'points', 'avatar')->get()->toArray();
        $sorted = $this->mergeSort($users, 'points');
        $leaderboard = array_values(array_reverse($sorted));
        $currentUser = Auth::user();
        $userRank = collect($leaderboard)->search(fn ($u) => $u['id'] === $currentUser->id) + 1;

        return view('student.leaderboard', compact('leaderboard', 'userRank', 'currentUser'));
    }

    public function discussion(Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (! $enrollment) {
            return redirect()->route('student.courses');
        }
        $posts = DiscussionPost::with('user', 'replies.user')->where('course_id', $course->id)->orderByDesc('is_pinned')->latest()->get();

        return view('student.discussion', compact('course', 'posts'));
    }

    public function postDiscussion(Request $request, Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (! $enrollment) {
            return redirect()->route('student.courses')->with('error', 'You must be enrolled to post discussions.');
        }

        $request->validate(['title' => 'required|max:255', 'body' => 'required']);
        DiscussionPost::create(['user_id' => Auth::id(), 'course_id' => $course->id, 'title' => $request->title, 'body' => $request->body]);
        $user->increment('points', 5);

        return redirect()->route('student.discussion', $course)->with('success', 'Discussion posted! +5 points');
    }

    public function replyDiscussion(Request $request, DiscussionPost $post)
    {
        $user = Auth::user();

        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $post->course_id)->first();
        if (! $enrollment) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to reply.');
        }

        $request->validate(['body' => 'required']);
        DiscussionReply::create(['post_id' => $post->id, 'user_id' => Auth::id(), 'body' => $request->body]);
        $user->increment('points', 2);

        return redirect()->back()->with('success', 'Reply posted! +2 points');
    }

    public function badges()
    {
        $user = Auth::user();
        $earnedBadges = $user->badges()->withPivot('earned_at')->get();
        $allBadges = Badge::all();
        $earnedIds = $earnedBadges->pluck('id');

        return view('student.badges', compact('user', 'earnedBadges', 'allBadges', 'earnedIds'));
    }

    private function mergeSort(array $arr, string $key): array
    {
        $n = count($arr);
        if ($n <= 1) {
            return $arr;
        }
        $mid = intdiv($n, 2);
        $left = $this->mergeSort(array_slice($arr, 0, $mid), $key);
        $right = $this->mergeSort(array_slice($arr, $mid), $key);

        return $this->merge($left, $right, $key);
    }

    private function merge(array $left, array $right, string $key): array
    {
        $result = [];
        $i = $j = 0;
        while ($i < count($left) && $j < count($right)) {
            if ($left[$i][$key] <= $right[$j][$key]) {
                $result[] = $left[$i++];
            } else {
                $result[] = $right[$j++];
            }
        }

        return array_merge($result, array_slice($left, $i), array_slice($right, $j));
    }

    private function getLeaderboardTop(int $limit): array
    {
        $users = User::role('student')->select('id', 'name', 'points', 'avatar')->get()->toArray();
        $sorted = array_reverse($this->mergeSort($users, 'points'));

        return array_slice(array_values($sorted), 0, $limit);
    }

    private function getUserRank(int $userId): int
    {
        $users = User::role('student')->orderByDesc('points')->pluck('id')->toArray();
        $rank = array_search($userId, $users);

        return $rank !== false ? $rank + 1 : 0;
    }

    private function checkAndAwardBadges(User $user): void
    {
        $user->refresh();
        $earned = $user->badges()->pluck('badges.id')->toArray();
        $badges = Badge::all();
        foreach ($badges as $badge) {
            if (in_array($badge->id, $earned)) {
                continue;
            }
            $award = match ($badge->criteria_type) {
                'points' => $user->points >= $badge->criteria_value,
                'lessons_completed' => UserProgress::where('user_id', $user->id)->where('is_completed', true)->count() >= $badge->criteria_value,
                'courses_completed' => Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count() >= $badge->criteria_value,
                'enrollments' => Enrollment::where('user_id', $user->id)->count() >= $badge->criteria_value,
                default => false,
            };
            if ($award) {
                DB::table('user_badges')->insertOrIgnore(['user_id' => $user->id, 'badge_id' => $badge->id, 'earned_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
