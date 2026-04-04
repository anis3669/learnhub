<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\UserProgress;
use App\Models\DiscussionPost;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = Auth::user();
        $courses = Course::where('teacher_id', $teacher->id)->withCount('enrollments', 'lessons')->get();
        $totalStudents = Enrollment::whereIn('course_id', $courses->pluck('id'))->distinct('user_id')->count();
        $totalAttempts = QuizAttempt::whereIn('quiz_id',
            Quiz::whereIn('course_id', $courses->pluck('id'))->pluck('id')
        )->whereNotNull('completed_at')->count();
        $recentEnrollments = Enrollment::with('user', 'course')
            ->whereIn('course_id', $courses->pluck('id'))->latest()->take(5)->get();
        return view('teacher.dashboard', compact('teacher', 'courses', 'totalStudents', 'totalAttempts', 'recentEnrollments'));
    }

    public function courses()
    {
        $courses = Course::where('teacher_id', Auth::id())->withCount('enrollments', 'lessons', 'quizzes')->latest()->get();
        return view('teacher.courses', compact('courses'));
    }

    public function createCourse()
    {
        return view('teacher.course-create');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category' => 'required',
            'level' => 'required',
        ]);
        $course = Course::create([
            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'level' => $request->level,
            'duration_hours' => $request->duration_hours ?? 0,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('teacher.course.show', $course)->with('success', 'Course created successfully!');
    }

    public function showCourse(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $lessons = $course->lessons()->withCount('progress')->get();
        $quizzes = $course->quizzes()->withCount('attempts')->get();
        $students = Enrollment::with('user')->where('course_id', $course->id)->latest()->get();
        return view('teacher.course-show', compact('course', 'lessons', 'quizzes', 'students'));
    }

    public function editCourse(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        return view('teacher.course-edit', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $request->validate(['title' => 'required|max:255', 'description' => 'required', 'category' => 'required', 'level' => 'required']);
        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'level' => $request->level,
            'duration_hours' => $request->duration_hours ?? 0,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('teacher.course.show', $course)->with('success', 'Course updated!');
    }

    public function createLesson(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $maxOrder = $course->lessons()->max('order') ?? 0;
        return view('teacher.lesson-create', compact('course', 'maxOrder'));
    }

    public function storeLesson(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $request->validate(['title' => 'required|max:255', 'video_url' => 'nullable|url']);
        $maxOrder = $course->lessons()->max('order') ?? 0;
        Lesson::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'content' => $request->content,
            'duration_minutes' => $request->duration_minutes ?? 0,
            'order' => $maxOrder + 1,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('teacher.course.show', $course)->with('success', 'Lesson added!');
    }

    public function editLesson(Course $course, Lesson $lesson)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        return view('teacher.lesson-edit', compact('course', 'lesson'));
    }

    public function updateLesson(Request $request, Course $course, Lesson $lesson)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $request->validate(['title' => 'required|max:255', 'video_url' => 'nullable|url']);
        $lesson->update([
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'content' => $request->content,
            'duration_minutes' => $request->duration_minutes ?? 0,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('teacher.course.show', $course)->with('success', 'Lesson updated!');
    }

    public function createQuiz(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $lessons = $course->lessons;
        return view('teacher.quiz-create', compact('course', 'lessons'));
    }

    public function storeQuiz(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $request->validate(['title' => 'required|max:255', 'time_limit_minutes' => 'required|integer|min:1', 'passing_score' => 'required|integer|min:1|max:100']);
        $quiz = Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $request->lesson_id ?: null,
            'title' => $request->title,
            'description' => $request->description,
            'time_limit_minutes' => $request->time_limit_minutes,
            'passing_score' => $request->passing_score,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('teacher.quiz.edit', [$course, $quiz])->with('success', 'Quiz created! Now add questions.');
    }

    public function editQuiz(Course $course, Quiz $quiz)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $questions = $quiz->questions()->with('options')->get();
        return view('teacher.quiz-edit', compact('course', 'quiz', 'questions'));
    }

    public function addQuestion(Request $request, Course $course, Quiz $quiz)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $request->validate(['question_text' => 'required', 'options' => 'required|array|min:2', 'correct_option' => 'required|integer']);
        $maxOrder = $quiz->questions()->max('order') ?? 0;
        $question = Question::create(['quiz_id' => $quiz->id, 'question_text' => $request->question_text, 'type' => 'multiple_choice', 'points' => $request->points ?? 10, 'order' => $maxOrder + 1]);
        foreach ($request->options as $i => $optText) {
            if (trim($optText)) {
                QuestionOption::create(['question_id' => $question->id, 'option_text' => $optText, 'is_correct' => ($i == $request->correct_option), 'order' => $i + 1]);
            }
        }
        return redirect()->back()->with('success', 'Question added!');
    }

    public function deleteQuestion(Course $course, Quiz $quiz, Question $question)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $question->delete();
        return redirect()->back()->with('success', 'Question deleted.');
    }

    public function studentProgress(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) abort(403);
        $enrollments = Enrollment::with('user')->where('course_id', $course->id)->get();
        $quizzes = $course->quizzes()->with('attempts.user')->get();
        $lessons = $course->lessons;
        $progressData = $enrollments->map(function($enrollment) use ($lessons, $quizzes) {
            $completedLessons = UserProgress::where('user_id', $enrollment->user_id)
                ->whereIn('lesson_id', $lessons->pluck('id'))->where('is_completed', true)->count();
            $quizAttempts = QuizAttempt::where('user_id', $enrollment->user_id)
                ->whereIn('quiz_id', $quizzes->pluck('id'))->whereNotNull('completed_at')->get();
            return ['enrollment' => $enrollment, 'completedLessons' => $completedLessons, 'quizAttempts' => $quizAttempts];
        });
        return view('teacher.progress', compact('course', 'progressData', 'lessons', 'quizzes'));
    }

    public function discussions()
    {
        $teacher = Auth::user();
        $courseIds = Course::where('teacher_id', $teacher->id)->pluck('id');
        $posts = DiscussionPost::with('user', 'course', 'replies')->whereIn('course_id', $courseIds)->latest()->paginate(15);
        return view('teacher.discussions', compact('posts'));
    }

    public function replyDiscussion(Request $request, DiscussionPost $post)
    {
        $request->validate(['body' => 'required']);
        DiscussionReply::create(['post_id' => $post->id, 'user_id' => Auth::id(), 'body' => $request->body]);
        return redirect()->back()->with('success', 'Reply posted!');
    }
}
