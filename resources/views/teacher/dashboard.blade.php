@extends('layouts.learnhub')
@section('title', 'Teacher Dashboard')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Teacher Dashboard')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link {{ request()->routeIs('teacher.courses') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    My Courses
</a>
<a href="{{ route('teacher.course.create') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Create Course
</a>
<a href="{{ route('teacher.discussions') }}" class="sidebar-link {{ request()->routeIs('teacher.discussions') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    Discussions
</a>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome, {{ $teacher->name }}! 👩‍🏫</h2>
                <p class="text-purple-100 mt-1">You have {{ $courses->count() }} courses and {{ $totalStudents }} students enrolled.</p>
                <a href="{{ route('teacher.course.create') }}" class="mt-4 inline-block bg-white text-purple-700 px-5 py-2 rounded-xl font-medium hover:bg-purple-50 transition">+ Create New Course</a>
            </div>
            <div class="hidden md:block text-8xl">👩‍🏫</div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card"><div class="text-3xl mb-1">📚</div><div class="text-2xl font-bold">{{ $courses->count() }}</div><div class="text-sm text-gray-500">Total Courses</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">👥</div><div class="text-2xl font-bold">{{ $totalStudents }}</div><div class="text-sm text-gray-500">Total Students</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">📝</div><div class="text-2xl font-bold">{{ $totalAttempts }}</div><div class="text-sm text-gray-500">Quiz Attempts</div></div>
        <div class="stat-card"><div class="text-3xl mb-1">🎬</div><div class="text-2xl font-bold">{{ $courses->sum('lessons_count') }}</div><div class="text-sm text-gray-500">Total Lessons</div></div>
    </div>

    {{-- Course Completion Statistics --}}
    @if($courseStats->count())
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">📊 Course Completion Statistics</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="pb-3 font-medium">Course</th>
                        <th class="pb-3 font-medium">Students</th>
                        <th class="pb-3 font-medium">Completed</th>
                        <th class="pb-3 font-medium">Completion Rate</th>
                        <th class="pb-3 font-medium">Quiz Pass Rate</th>
                        <th class="pb-3 font-medium">Avg Score</th>
                        <th class="pb-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courseStats as $stat)
                    <tr class="border-b border-gray-50">
                        <td class="py-3">
                            <div class="font-medium text-gray-800">{{ Str::limit($stat['course']->title, 35) }}</div>
                            <div class="text-xs text-gray-500">{{ $stat['course']->level }}</div>
                        </td>
                        <td class="py-3 text-gray-600">{{ $stat['total'] }}</td>
                        <td class="py-3 text-green-600 font-medium">{{ $stat['completed'] }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $stat['rate'] >= 70 ? 'bg-green-500' : ($stat['rate'] >= 40 ? 'bg-amber-500' : 'bg-red-400') }}" style="width: {{ $stat['rate'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium">{{ $stat['rate'] }}%</span>
                            </div>
                        </td>
                        <td class="py-3">
                            @if($stat['passRate'] !== null)
                            <span class="badge-pill {{ $stat['passRate'] >= 70 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $stat['passRate'] }}%</span>
                            @else
                            <span class="text-gray-400 text-xs">No quizzes</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($stat['avgScore'] !== null)
                            <span class="font-medium text-indigo-600">{{ $stat['avgScore'] }}%</span>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <a href="{{ route('teacher.progress', $stat['course']) }}" class="text-indigo-600 hover:underline text-xs">Progress →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Quiz Analytics --}}
        @if($quizAnalytics->count())
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">🎯 Quiz Analytics</h3>
            @foreach($quizAnalytics->take(6) as $qa)
            <div class="mb-4 p-3 bg-gray-50 rounded-xl">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $qa['quiz']->title }}</div>
                        <div class="text-xs text-gray-500">{{ $qa['total'] }} attempts</div>
                    </div>
                    <span class="badge-pill {{ $qa['passRate'] >= 70 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $qa['passRate'] }}% pass</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $qa['avgScore'] }}%"></div>
                    </div>
                    <span class="text-xs text-gray-600 w-12 text-right">avg {{ $qa['avgScore'] }}%</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Recent Enrollments --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">👥 Student Progress</h3>
                <a href="{{ route('teacher.courses') }}" class="text-indigo-600 text-xs">View Courses</a>
            </div>
            @forelse($recentEnrollments as $enrollment)
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-indigo-200 flex items-center justify-center text-sm font-bold text-indigo-700 flex-shrink-0">
                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-800 truncate">{{ $enrollment->user->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $enrollment->course->title }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                        <div class="h-1 rounded-full bg-indigo-500" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                </div>
                <div class="text-xs font-medium text-indigo-600">{{ $enrollment->progress_percent }}%</div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">No enrollments yet</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Discussions --}}
    @if($recentDiscussions->count())
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">💬 Recent Course Discussions</h3>
            <a href="{{ route('teacher.discussions') }}" class="text-indigo-600 text-sm">View All</a>
        </div>
        <div class="grid lg:grid-cols-2 gap-3">
            @foreach($recentDiscussions as $post)
            <div class="p-4 bg-gray-50 rounded-xl">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-indigo-200 rounded-full flex items-center justify-center text-xs font-bold text-indigo-700 flex-shrink-0">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $post->title }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $post->course->title }} • {{ $post->created_at->diffForHumans() }}</div>
                        <div class="text-xs text-indigo-600 mt-1">{{ $post->replies->count() }} replies</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
