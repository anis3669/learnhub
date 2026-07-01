@extends('layouts.learnhub')
@section('title', 'Course Access Management')
@section('portal-name', 'Admin Panel')
@section('page-title', 'Course Access Management')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('admin.users') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    Users
</a>
<a href="{{ route('admin.courses') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Courses
</a>
<a href="{{ route('admin.course-permissions') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
    Course Access
</a>
<a href="{{ route('admin.reports') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    Reports
</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Badges
</a>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-700 to-purple-700 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold">🔑 Course Access Management</h2>
        <p class="text-indigo-200 mt-1">Grant students access to courses regardless of their learning path level. Admin overrides always take precedence.</p>
    </div>

    {{-- Grant Access Form --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Grant Course Access</h3>
        <form action="{{ route('admin.course-permissions.grant') }}" method="POST" class="grid md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select student...</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                <select name="course_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select course...</option>
                    @foreach($courses->groupBy('level') as $level => $levelCourses)
                    <optgroup label="{{ $level }}">
                        @foreach($levelCourses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason (optional)</label>
                <input type="text" name="reason" placeholder="e.g. Special exemption" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="btn-primary">🔓 Grant Access</button>
            </div>
        </form>
    </div>

    {{-- Active Permissions --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Active Access Grants ({{ $permissions->total() }})</h3>
        @if($permissions->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="pb-3 font-medium">Student</th>
                        <th class="pb-3 font-medium">Course</th>
                        <th class="pb-3 font-medium">Level</th>
                        <th class="pb-3 font-medium">Granted By</th>
                        <th class="pb-3 font-medium">Reason</th>
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $perm)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3">
                            <div class="font-medium text-gray-800">{{ $perm->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $perm->user->email }}</div>
                        </td>
                        <td class="py-3">
                            <div class="font-medium text-gray-800">{{ Str::limit($perm->course->title, 35) }}</div>
                        </td>
                        <td class="py-3">
                            <span class="badge-pill {{ match($perm->course->level) { 'Introduction' => 'bg-blue-100 text-blue-700', 'Beginner' => 'bg-green-100 text-green-700', 'Intermediate' => 'bg-amber-100 text-amber-700', 'Advanced' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-700' } }}">
                                {{ $perm->course->level }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-600">{{ $perm->grantedBy->name }}</td>
                        <td class="py-3 text-gray-500 max-w-xs">{{ $perm->reason ?: '—' }}</td>
                        <td class="py-3 text-gray-400">{{ $perm->created_at->format('M d, Y') }}</td>
                        <td class="py-3">
                            <form action="{{ route('admin.course-permissions.revoke', $perm) }}" method="POST" onsubmit="return confirm('Revoke access?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Revoke</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $permissions->links() }}</div>
        @else
        <div class="text-center py-8 text-gray-400">
            <div class="text-4xl mb-2">🔐</div>
            <p>No active access grants yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection
