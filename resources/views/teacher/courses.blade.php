@extends('layouts.learnhub')
@section('title', 'My Courses')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'My Courses')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link active">My Courses</a>
<a href="{{ route('teacher.course.create') }}" class="sidebar-link">+ Create Course</a>
<a href="{{ route('teacher.discussions') }}" class="sidebar-link">Discussions</a>
@endsection

@section('header-actions')
<a href="{{ route('teacher.course.create') }}" class="btn-primary">+ New Course</a>
@endsection

@section('content')
<div class="space-y-4">
    @forelse($courses as $course)
    <div class="card p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">📖</div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold text-gray-900">{{ $course->title }}</h3>
                        <span class="badge-pill {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $course->is_published ? '🟢 Published' : '🟡 Draft' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-2">{{ Str::limit($course->description, 100) }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span>📚 {{ $course->category }}</span>
                        <span>⭐ {{ $course->level }}</span>
                        <span>🎬 {{ $course->lessons_count }} lessons</span>
                        <span>👥 {{ $course->enrollments_count }} students</span>
                        <span>🧠 {{ $course->quizzes_count }} quizzes</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('teacher.course.show', $course) }}" class="btn-secondary">Manage</a>
                <a href="{{ route('teacher.course.edit', $course) }}" class="btn-primary">Edit</a>
                <a href="{{ route('teacher.progress', $course) }}" class="bg-purple-100 text-purple-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-purple-200 transition">Progress</a>
            </div>
        </div>
    </div>
    @empty
    <div class="card p-16 text-center text-gray-400">
        <div class="text-5xl mb-3">📚</div>
        <p class="text-lg font-medium">No courses yet</p>
        <a href="{{ route('teacher.course.create') }}" class="btn-primary mt-4 inline-block">Create Your First Course</a>
    </div>
    @endforelse
</div>
@endsection
