@extends('layouts.learnhub')
@section('title', 'Create Course')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Create New Course')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link">My Courses</a>
<a href="{{ route('teacher.course.create') }}" class="sidebar-link active">+ Create Course</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">📚 Create New Course</h2>
        <form action="{{ route('teacher.course.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Introduction to Python Programming">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" required rows="4" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Describe what students will learn...">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['Programming', 'Web Development', 'Computer Science', 'AI & ML', 'Data Science', 'Mobile Development', 'General'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                    <select name="level" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['Beginner', 'Intermediate', 'Advanced'] as $l)
                        <option value="{{ $l }}" {{ old('level') == $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Duration (hours)</label>
                <input type="number" name="duration_hours" value="{{ old('duration_hours', 0) }}" min="0" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="published" {{ old('is_published') ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                <label for="published" class="text-sm text-gray-700">Publish immediately (visible to students)</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Create Course</button>
                <a href="{{ route('teacher.courses') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
