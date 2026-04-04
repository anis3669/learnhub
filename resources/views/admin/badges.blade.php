@extends('layouts.learnhub')
@section('title', 'Manage Badges')
@section('portal-name', 'Admin Panel')
@section('page-title', '🎖️ Manage Badges')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('admin.users') }}" class="sidebar-link">Users</a>
<a href="{{ route('admin.courses') }}" class="sidebar-link">Courses</a>
<a href="{{ route('admin.reports') }}" class="sidebar-link">Reports</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link active">Badges</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <!-- Existing badges -->
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-900">Current Badges ({{ $badges->count() }})</h3>
        @foreach($badges as $badge)
        <div class="card p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl">{{ $badge->icon }}</div>
                <div>
                    <div class="font-medium text-gray-900">{{ $badge->name }}</div>
                    <div class="text-xs text-gray-500">{{ $badge->description }}</div>
                    <div class="text-xs text-indigo-600 mt-1">{{ $badge->criteria_type }}: {{ $badge->criteria_value }} • {{ $badge->users_count }} earned</div>
                </div>
            </div>
            <form action="{{ route('admin.badge.delete', $badge) }}" method="POST" onsubmit="return confirm('Delete this badge?')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-xs px-3 py-1.5 rounded hover:bg-red-50">Delete</button>
            </form>
        </div>
        @endforeach
    </div>

    <!-- Create badge form -->
    <div class="card p-6 h-fit">
        <h3 class="font-semibold text-gray-900 mb-5">➕ Create New Badge</h3>
        <form action="{{ route('admin.badge.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Badge Name *</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Quick Learner">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" required rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (emoji) *</label>
                    <input type="text" name="icon" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm" placeholder="🎯">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <select name="color" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                        @foreach(['yellow', 'blue', 'green', 'red', 'purple', 'orange', 'indigo'] as $c)
                        <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Criteria Type *</label>
                    <select name="criteria_type" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                        <option value="points">Points</option>
                        <option value="lessons_completed">Lessons Completed</option>
                        <option value="courses_completed">Courses Completed</option>
                        <option value="enrollments">Enrollments</option>
                        <option value="quiz_perfect">Perfect Quiz</option>
                        <option value="discussions">Discussions</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Value *</label>
                    <input type="number" name="criteria_value" required min="1" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm" placeholder="e.g. 500">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full">Create Badge</button>
        </form>
    </div>
</div>
@endsection
