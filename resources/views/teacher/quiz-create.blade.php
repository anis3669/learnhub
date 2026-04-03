@extends('layouts.learnhub')
@section('title', 'Create Quiz')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Create Quiz')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">🧠 Create New Quiz</h2>
        <form action="{{ route('teacher.quiz.store', $course) }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Python Basics Quiz">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="What is this quiz about?">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Related Lesson (optional)</label>
                <select name="lesson_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">-- Course-wide quiz --</option>
                    @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (minutes) *</label>
                    <input type="number" name="time_limit_minutes" required value="{{ old('time_limit_minutes', 15) }}" min="1" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score (%) *</label>
                    <input type="number" name="passing_score" required value="{{ old('passing_score', 60) }}" min="1" max="100" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="pub" checked class="w-4 h-4 text-indigo-600 rounded">
                <label for="pub" class="text-sm">Publish (students can take this quiz)</label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Create & Add Questions</button>
                <a href="{{ route('teacher.course.show', $course) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
