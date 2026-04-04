<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\UserProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\AttemptAnswer;
use App\Models\QuestionOption;
use App\Models\Badge;
use App\Models\DiscussionPost;
use App\Models\DiscussionReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
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
        return view('student.dashboard', compact('user', 'enrollments', 'recentAttempts', 'badges', 'leaderboard', 'rank'));
    }

    public function courses(Request $request)
    {
        $user = Auth::user();
        $query = Course::with('teacher')->where('is_published', true);
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
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
        return view('student.courses', compact('courses', 'enrolledIds', 'categories'));
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();
        Enrollment::firstOrCreate(['user_id' => $user->id, 'course_id' => $course->id], ['progress_percent' => 0]);
        return redirect()->route('student.course.show', $course)->with('success', "Enrolled in \"{$course->title}\" successfully!");
    }

    public function showCourse(Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('student.courses')->with('error', 'Please enroll in the course first.');
        }
        $lessons = $course->lessons()->with(['progress' => fn($q) => $q->where('user_id', $user->id)])->get();
        $quizzes = $course->quizzes()->where('is_published', true)->with(['attempts' => fn($q) => $q->where('user_id', $user->id)])->get();
        $posts = DiscussionPost::with('user', 'replies')->where('course_id', $course->id)->latest()->take(5)->get();
        $completedCount = $lessons->filter(fn($l) => $l->progress->isNotEmpty() && $l->progress->first()->is_completed)->count();
        $progress = $lessons->count() > 0 ? round(($completedCount / $lessons->count()) * 100) : 0;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->update(['progress_percent' => $progress]);
        return view('student.course-show', compact('course', 'enrollment', 'lessons', 'quizzes', 'posts', 'progress'));
    }

    public function watchLesson(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (!$enrollment) return redirect()->route('student.courses');
        $prev = $course->lessons()->where('order', '<', $lesson->order)->orderByDesc('order')->first();
        $next = $course->lessons()->where('order', '>', $lesson->order)->orderBy('order')->first();
        $userProgress = UserProgress::firstOrCreate(['user_id' => $user->id, 'lesson_id' => $lesson->id], ['watch_percent' => 0]);
        $quiz = $lesson->quiz()->where('is_published', true)->first();
        return view('student.lesson', compact('course', 'lesson', 'prev', 'next', 'userProgress', 'quiz'));
    }

    public function markComplete(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        UserProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => true, 'watch_percent' => 100, 'completed_at' => now()]
        );
        $user->increment('points', 20);
        $this->checkAndAwardBadges($user);
        $totalLessons = $course->lessons()->count();
        $completedLessons = UserProgress::where('user_id', $user->id)->whereIn('lesson_id', $course->lessons()->pluck('id'))->where('is_completed', true)->count();
        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->update(['progress_percent' => $progress]);
        $next = $course->lessons()->where('order', '>', $lesson->order)->orderBy('order')->first();
        if ($next) return redirect()->route('student.lesson', [$course, $next])->with('success', 'Lesson completed! +20 points 🎉');
        return redirect()->route('student.course.show', $course)->with('success', 'Lesson completed! +20 points 🎉');
    }

    public function takeQuiz(Course $course, Quiz $quiz)
    {
        $user = Auth::user();
        $questions = $quiz->questions()->with('options')->get();
        if ($questions->isEmpty()) return redirect()->back()->with('error', 'This quiz has no questions yet.');
        $attempt = QuizAttempt::create(['user_id' => $user->id, 'quiz_id' => $quiz->id, 'started_at' => now()]);
        return view('student.quiz', compact('course', 'quiz', 'questions', 'attempt'));
    }

    public function submitQuiz(Request $request, Course $course, Quiz $quiz)
    {
        $user = Auth::user();
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
                if ($isCorrect) $score += $question->points;
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
        if ($attempt->user_id !== Auth::id()) abort(403);
        $answers = $attempt->answers()->with('question.options', 'selectedOption')->get();
        return view('student.quiz-result', compact('course', 'quiz', 'attempt', 'answers'));
    }

    public function leaderboard()
    {
        $users = User::role('student')->select('id', 'name', 'points', 'avatar')->get()->toArray();
        $sorted = $this->mergeSort($users, 'points');
        $leaderboard = array_values(array_reverse($sorted));
        $currentUser = Auth::user();
        $userRank = collect($leaderboard)->search(fn($u) => $u['id'] === $currentUser->id) + 1;
        return view('student.leaderboard', compact('leaderboard', 'userRank', 'currentUser'));
    }

    public function discussion(Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (!$enrollment) return redirect()->route('student.courses');
        $posts = DiscussionPost::with('user', 'replies.user')->where('course_id', $course->id)->orderByDesc('is_pinned')->latest()->get();
        return view('student.discussion', compact('course', 'posts'));
    }

    public function postDiscussion(Request $request, Course $course)
    {
        $request->validate(['title' => 'required|max:255', 'body' => 'required']);
        DiscussionPost::create(['user_id' => Auth::id(), 'course_id' => $course->id, 'title' => $request->title, 'body' => $request->body]);
        Auth::user()->increment('points', 5);
        return redirect()->route('student.discussion', $course)->with('success', 'Discussion posted! +5 points');
    }

    public function replyDiscussion(Request $request, DiscussionPost $post)
    {
        $request->validate(['body' => 'required']);
        DiscussionReply::create(['post_id' => $post->id, 'user_id' => Auth::id(), 'body' => $request->body]);
        Auth::user()->increment('points', 2);
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
        if ($n <= 1) return $arr;
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
            if (in_array($badge->id, $earned)) continue;
            $award = match($badge->criteria_type) {
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
