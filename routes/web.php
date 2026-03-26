<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Leaderboard
Route::get('/leaderboard', function () {
    return view('leaderboard');
})->name('leaderboard');

// ====================== ROLE PROTECTED ROUTES ======================
Route::middleware('auth')->group(function () {

    // STUDENT DASHBOARD - Only Student can access
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', function () {
            return view('livewire.student-dashboard');
        })->name('student.dashboard');
    });

    // TEACHER DASHBOARD - Only Teacher can access
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher/dashboard', function () {
            return view('livewire.teacher-dashboard');
        })->name('teacher.dashboard');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';