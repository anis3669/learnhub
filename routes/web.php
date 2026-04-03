<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
        return redirect()->route('student.dashboard');
    }
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function() {
        $user = auth()->user();
        if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [StudentController::class, 'courses'])->name('courses');
    Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll'])->name('enroll');
    Route::get('/courses/{course}', [StudentController::class, 'showCourse'])->name('course.show');
    Route::get('/courses/{course}/lessons/{lesson}', [StudentController::class, 'watchLesson'])->name('lesson');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [StudentController::class, 'markComplete'])->name('lesson.complete');
    Route::get('/courses/{course}/quiz/{quiz}', [StudentController::class, 'takeQuiz'])->name('quiz');
    Route::post('/courses/{course}/quiz/{quiz}/submit', [StudentController::class, 'submitQuiz'])->name('quiz.submit');
    Route::get('/courses/{course}/quiz/{quiz}/result/{attempt}', [StudentController::class, 'quizResult'])->name('quiz.result');
    Route::get('/courses/{course}/discussion', [StudentController::class, 'discussion'])->name('discussion');
    Route::post('/courses/{course}/discussion', [StudentController::class, 'postDiscussion'])->name('discussion.post');
    Route::post('/discussion/{post}/reply', [StudentController::class, 'replyDiscussion'])->name('discussion.reply');
    Route::get('/leaderboard', [StudentController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/badges', [StudentController::class, 'badges'])->name('badges');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [TeacherController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [TeacherController::class, 'createCourse'])->name('course.create');
    Route::post('/courses', [TeacherController::class, 'storeCourse'])->name('course.store');
    Route::get('/courses/{course}', [TeacherController::class, 'showCourse'])->name('course.show');
    Route::get('/courses/{course}/edit', [TeacherController::class, 'editCourse'])->name('course.edit');
    Route::patch('/courses/{course}', [TeacherController::class, 'updateCourse'])->name('course.update');
    Route::get('/courses/{course}/lessons/create', [TeacherController::class, 'createLesson'])->name('lesson.create');
    Route::post('/courses/{course}/lessons', [TeacherController::class, 'storeLesson'])->name('lesson.store');
    Route::get('/courses/{course}/lessons/{lesson}/edit', [TeacherController::class, 'editLesson'])->name('lesson.edit');
    Route::patch('/courses/{course}/lessons/{lesson}', [TeacherController::class, 'updateLesson'])->name('lesson.update');
    Route::get('/courses/{course}/quiz/create', [TeacherController::class, 'createQuiz'])->name('quiz.create');
    Route::post('/courses/{course}/quiz', [TeacherController::class, 'storeQuiz'])->name('quiz.store');
    Route::get('/courses/{course}/quiz/{quiz}/edit', [TeacherController::class, 'editQuiz'])->name('quiz.edit');
    Route::post('/courses/{course}/quiz/{quiz}/question', [TeacherController::class, 'addQuestion'])->name('quiz.question.add');
    Route::delete('/courses/{course}/quiz/{quiz}/question/{question}', [TeacherController::class, 'deleteQuestion'])->name('quiz.question.delete');
    Route::get('/courses/{course}/progress', [TeacherController::class, 'studentProgress'])->name('progress');
    Route::get('/discussions', [TeacherController::class, 'discussions'])->name('discussions');
    Route::post('/discussion/{post}/reply', [TeacherController::class, 'replyDiscussion'])->name('discussion.reply');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('user.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('user.delete');
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::patch('/courses/{course}/toggle', [AdminController::class, 'toggleCourse'])->name('course.toggle');
    Route::delete('/courses/{course}', [AdminController::class, 'deleteCourse'])->name('course.delete');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/badges', [AdminController::class, 'badges'])->name('badges');
    Route::post('/badges', [AdminController::class, 'storeBadge'])->name('badge.store');
    Route::delete('/badges/{badge}', [AdminController::class, 'deleteBadge'])->name('badge.delete');
});

require __DIR__.'/auth.php';
