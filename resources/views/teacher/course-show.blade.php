@extends('layouts.learnhub')
@section('title', $course->title)
@section('portal-name', 'Teacher Portal')
@section('page-title', $course->title)
@section('breadcrumb', 'My Courses → ' . $course->title)

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link">My Courses</a>
<a href="{{ route('teacher.discussions') }}" class="sidebar-link">Discussions</a>
@endsection

@section('header-actions')
<a href="{{ route('teacher.course.edit', $course) }}" class="btn-primary">Edit Course</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Course info -->
    <div class="card p-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge-pill bg-indigo-100 text-indigo-700">{{ $course->category }}</span>
                    <span class="badge-pill {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $course->is_published ? '🟢 Published' : '🟡 Draft' }}
                    </span>
                </div>
                <p class="text-gray-600">{{ $course->description }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('teacher.lesson.create', $course) }}" class="btn-primary">+ Add Lesson</a>
                <a href="{{ route('teacher.quiz.create', $course) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition">+ Add Quiz</a>
                <a href="{{ route('teacher.progress', $course) }}" class="btn-secondary">📊 Progress</a>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-4 mt-4">
            <div class="text-center p-3 bg-gray-50 rounded-xl"><div class="text-xl font-bold text-gray-800">{{ $students->count() }}</div><div class="text-xs text-gray-500">Students</div></div>
            <div class="text-center p-3 bg-gray-50 rounded-xl"><div class="text-xl font-bold text-gray-800">{{ $lessons->count() }}</div><div class="text-xs text-gray-500">Lessons</div></div>
            <div class="text-center p-3 bg-gray-50 rounded-xl"><div class="text-xl font-bold text-gray-800">{{ $quizzes->count() }}</div><div class="text-xs text-gray-500">Quizzes</div></div>
            <div class="text-center p-3 bg-gray-50 rounded-xl"><div class="text-xl font-bold text-gray-800">{{ $quizzes->sum('attempts_count') }}</div><div class="text-xs text-gray-500">Attempts</div></div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Lessons -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">📹 Lessons</h3>
                <a href="{{ route('teacher.lesson.create', $course) }}" class="text-indigo-600 text-sm hover:underline">+ Add</a>
            </div>
            @forelse($lessons as $lesson)
            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-indigo-200 mb-2 group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-sm font-bold text-indigo-600">{{ $lesson->order }}</div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $lesson->title }}</div>
                        <div class="text-xs text-gray-400">{{ $lesson->duration_minutes }}min • {{ $lesson->progress_count }} watched</div>
                    </div>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                    <a href="{{ route('teacher.lesson.edit', [$course, $lesson]) }}" class="text-indigo-600 hover:bg-indigo-50 p-1 rounded text-xs">Edit</a>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-6">No lessons yet. <a href="{{ route('teacher.lesson.create', $course) }}" class="text-indigo-600">Add the first lesson!</a></p>
            @endforelse
        </div>

        <!-- Quizzes -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">🧠 Quizzes</h3>
                <a href="{{ route('teacher.quiz.create', $course) }}" class="text-indigo-600 text-sm hover:underline">+ Add</a>
            </div>
            @forelse($quizzes as $quiz)
            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-purple-200 mb-2">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $quiz->title }}</div>
                    <div class="text-xs text-gray-400">{{ $quiz->attempts_count }} attempts • Pass: {{ $quiz->passing_score }}%</div>
                </div>
                <a href="{{ route('teacher.quiz.edit', [$course, $quiz]) }}" class="text-purple-600 hover:underline text-xs">Edit</a>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-6">No quizzes yet. <a href="{{ route('teacher.quiz.create', $course) }}" class="text-indigo-600">Create one!</a></p>
            @endforelse
        </div>
    </div>

    <!-- Students -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">👥 Enrolled Students</h3>
        @forelse($students->take(10) as $enrollment)
        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-200 flex items-center justify-center text-sm font-bold text-indigo-700">
                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $enrollment->user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $enrollment->user->email }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-medium text-indigo-600">{{ $enrollment->progress_percent }}%</div>
                <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-6">No students enrolled yet.</p>
        @endforelse
        @if($students->count() > 10)
        <a href="{{ route('teacher.progress', $course) }}" class="text-indigo-600 text-sm hover:underline mt-2 block">View all {{ $students->count() }} students →</a>
        @endif
    </div>
</div>
@endsection
