@extends('layouts.learnhub')
@section('title', 'Edit Lesson')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Edit Lesson')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">✏️ Edit Lesson</h2>
        <form action="{{ route('teacher.lesson.update', [$course, $lesson]) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $lesson->title) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">{{ old('description', $lesson->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content / Notes</label>
                <textarea name="content" rows="5" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">{{ old('content', $lesson->content) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="pub" {{ $lesson->is_published ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                <label for="pub" class="text-sm">Published</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('teacher.course.show', $course) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
