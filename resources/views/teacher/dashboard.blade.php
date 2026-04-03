@extends('layouts.learnhub')
@section('title', 'Teacher Dashboard')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Teacher Dashboard')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    My Courses
</a>
<a href="{{ route('teacher.course.create') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Create Course
</a>
<a href="{{ route('teacher.discussions') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    Discussions
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome, {{ $teacher->name }}! 👩‍🏫</h2>
                <p class="text-purple-100 mt-1">You have {{ $courses->count() }} courses and {{ $totalStudents }} students enrolled.</p>
                <a href="{{ route('teacher.course.create') }}" class="mt-4 inline-block bg-white text-purple-700 px-5 py-2 rounded-xl font-medium hover:bg-purple-50 transition">+ Create New Course</a>
            </div>
            <div class="hidden md:block text-8xl">👩‍🏫</div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card"><div class="text-3xl mb-1">📚</div><div class="text-2xl font-bold">{{ $courses->count() }}</div><div class="text-sm text-gray-500">Total Courses</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">👥</div><div class="text-2xl font-bold">{{ $totalStudents }}</div><div class="text-sm text-gray-500">Total Students</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">📝</div><div class="text-2xl font-bold">{{ $totalAttempts }}</div><div class="text-sm text-gray-500">Quiz Attempts</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">🎬</div><div class="text-2xl font-bold">{{ $courses->sum('lessons_count') }}</div><div class="text-sm text-gray-500">Total Lessons</div></div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- My Courses -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">My Courses</h3>
                <a href="{{ route('teacher.courses') }}" class="text-indigo-600 text-sm">View All</a>
            </div>
            @forelse($courses->take(5) as $course)
            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-xl">📖</div>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">{{ Str::limit($course->title, 35) }}</div>
                        <div class="text-xs text-gray-500">{{ $course->enrollments_count }} students • {{ $course->lessons_count }} lessons</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge-pill {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $course->is_published ? 'Live' : 'Draft' }}
                    </span>
                    <a href="{{ route('teacher.course.show', $course) }}" class="text-indigo-600 hover:underline text-xs">Manage</a>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p>No courses yet. <a href="{{ route('teacher.course.create') }}" class="text-indigo-600">Create one!</a></p>
            </div>
            @endforelse
        </div>

        <!-- Recent Enrollments -->
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Recent Enrollments</h3>
            @forelse($recentEnrollments as $enrollment)
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-indigo-200 flex items-center justify-center text-sm font-bold text-indigo-700 flex-shrink-0">
                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-800 truncate">{{ $enrollment->user->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $enrollment->course->title }}</div>
                </div>
                <div class="text-xs text-gray-400 flex-shrink-0">{{ $enrollment->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">No enrollments yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
