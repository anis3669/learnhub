@extends('layouts.learnhub')
@section('title', 'Edit Course')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Edit Course')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link">My Courses</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">✏️ Edit Course</h2>
        <form action="{{ route('teacher.course.update', $course) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
                <input type="text" name="title" required value="{{ old('title', $course->title) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" required rows="4" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $course->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                        @foreach(['Programming', 'Web Development', 'Computer Science', 'AI & ML', 'Data Science', 'Mobile Development', 'General'] as $cat)
                        <option value="{{ $cat }}" {{ $course->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <select name="level" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                        @foreach(['Beginner', 'Intermediate', 'Advanced'] as $l)
                        <option value="{{ $l }}" {{ $course->level == $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                <input type="number" name="duration_hours" value="{{ $course->duration_hours }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="published" {{ $course->is_published ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                <label for="published" class="text-sm text-gray-700">Published (visible to students)</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('teacher.course.show', $course) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
