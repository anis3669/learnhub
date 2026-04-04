@extends('layouts.learnhub')
@section('title', 'Admin Dashboard')
@section('portal-name', 'Admin Panel')
@section('page-title', 'Admin Dashboard')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link active">
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
<a href="{{ route('admin.reports') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    Reports
</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
    Badges
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Admin Control Panel ⚙️</h2>
                <p class="text-slate-300 mt-1">Platform overview and management tools</p>
            </div>
            <div class="hidden md:block text-8xl">🖥️</div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card"><div class="text-3xl mb-1">👨‍🎓</div><div class="text-3xl font-bold text-indigo-600">{{ $totalStudents }}</div><div class="text-sm text-gray-500">Students</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">👩‍🏫</div><div class="text-3xl font-bold text-purple-600">{{ $totalTeachers }}</div><div class="text-sm text-gray-500">Teachers</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">📚</div><div class="text-3xl font-bold text-green-600">{{ $totalCourses }}</div><div class="text-sm text-gray-500">Courses</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">🎓</div><div class="text-3xl font-bold text-orange-600">{{ $totalEnrollments }}</div><div class="text-sm text-gray-500">Enrollments</div></div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">👥 Recent Users</h3>
                <a href="{{ route('admin.users') }}" class="text-indigo-600 text-sm">View All</a>
            </div>
            @foreach($recentUsers as $user)
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-indigo-200 flex items-center justify-center text-sm font-bold text-indigo-700">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                </div>
                <div>
                    @foreach($user->roles as $role)
                    <span class="badge-pill {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' : ($role->name === 'teacher' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
            <a href="{{ route('admin.user.create') }}" class="block text-center btn-primary mt-4 text-sm">+ Add New User</a>
        </div>

        <!-- Popular Courses -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">🔥 Popular Courses</h3>
                <a href="{{ route('admin.courses') }}" class="text-indigo-600 text-sm">View All</a>
            </div>
            @foreach($popularCourses as $course)
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center text-lg">📖</div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ Str::limit($course->title, 35) }}</div>
                        <div class="text-xs text-gray-500">{{ $course->teacher->name }}</div>
                    </div>
                </div>
                <span class="badge-pill bg-green-100 text-green-700">{{ $course->enrollments_count }} students</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Stats -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">📈 Monthly Activity (Last 6 Months)</h3>
        <div class="grid grid-cols-6 gap-4">
            @foreach($monthlyEnrollments as $stat)
            <div class="text-center">
                <div class="text-lg font-bold text-indigo-600">{{ $stat['enrollments'] }}</div>
                <div class="text-xs text-gray-500">enrollments</div>
                <div class="text-sm font-medium text-gray-700 mt-1">{{ $stat['month'] }}</div>
                <div class="text-xs text-green-600">+{{ $stat['users'] }} users</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Quiz Attempts -->
    @if($recentAttempts->count())
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">📝 Recent Quiz Attempts</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-3">Student</th>
                        <th class="pb-3">Quiz</th>
                        <th class="pb-3">Score</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttempts as $attempt)
                    <tr class="border-b border-gray-50">
                        <td class="py-3 font-medium">{{ $attempt->user->name }}</td>
                        <td class="py-3 text-gray-600">{{ $attempt->quiz->title }}</td>
                        <td class="py-3 font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">{{ $attempt->score_percent }}%</td>
                        <td class="py-3"><span class="badge-pill {{ $attempt->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $attempt->passed ? 'Passed' : 'Failed' }}</span></td>
                        <td class="py-3 text-gray-400">{{ $attempt->completed_at?->format('M d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
