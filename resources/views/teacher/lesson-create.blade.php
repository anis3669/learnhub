@extends('layouts.learnhub')
@section('title', 'Add Lesson')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Add New Lesson')
@section('breadcrumb', $course->title . ' → New Lesson')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link">My Courses</a>
<a href="{{ route('teacher.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">🎬 Add New Lesson</h2>
        <form action="{{ route('teacher.lesson.store', $course) }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Introduction to Variables">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Brief description of this lesson...">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Video URL (YouTube or Vimeo)</label>
                <input type="url" name="video_url" value="{{ old('video_url') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="https://www.youtube.com/watch?v=...">
                <p class="text-xs text-gray-400 mt-1">Supports YouTube (youtube.com/watch?v= or youtu.be/) and Vimeo (vimeo.com/) URLs</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Notes / Content</label>
                <textarea name="content" rows="5" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Additional notes, reading material, code examples...">{{ old('content') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 0) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="pub" checked class="w-4 h-4 text-indigo-600 rounded">
                <label for="pub" class="text-sm text-gray-700">Publish this lesson</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Add Lesson</button>
                <a href="{{ route('teacher.course.show', $course) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
