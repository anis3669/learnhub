@extends('layouts.learnhub')
@section('title', 'Manage Courses')
@section('portal-name', 'Admin Panel')
@section('page-title', 'Manage Courses')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('admin.users') }}" class="sidebar-link">Users</a>
<a href="{{ route('admin.courses') }}" class="sidebar-link active">Courses</a>
<a href="{{ route('admin.reports') }}" class="sidebar-link">Reports</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link">Badges</a>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filter -->
    <form method="GET" class="card p-4">
        <div class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses..." class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search'))<a href="{{ route('admin.courses') }}" class="btn-secondary">Clear</a>@endif
        </div>
    </form>

    <!-- Courses table -->
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Course</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Teacher</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Stats</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($courses as $course)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ Str::limit($course->title, 45) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $course->category }} • {{ $course->level }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $course->teacher->name }}</td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-gray-500">👥 {{ $course->enrollments_count }} students</div>
                        <div class="text-xs text-gray-500">🎬 {{ $course->lessons_count }} lessons</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="badge-pill {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $course->is_published ? '🟢 Published' : '🟡 Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.course.toggle', $course) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs {{ $course->is_published ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">
                                    {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.course.delete', $course) }}" method="POST" onsubmit="return confirm('Delete this course and all its content?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No courses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $courses->links() }}
</div>
@endsection
