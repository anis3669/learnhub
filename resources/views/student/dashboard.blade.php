@extends('layouts.learnhub')

@section('title', 'Student Dashboard')
@section('portal-name', 'Student Portal')
@section('page-title', 'My Dashboard')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.courses') }}" class="sidebar-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Browse Courses
</a>
<a href="{{ route('student.leaderboard') }}" class="sidebar-link {{ request()->routeIs('student.leaderboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    Leaderboard
</a>
<a href="{{ route('student.badges') }}" class="sidebar-link {{ request()->routeIs('student.badges') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
    My Badges
</a>
<div class="pt-2 pb-1 px-1 text-xs text-gray-400 uppercase tracking-wider font-medium">My Courses</div>
@foreach($enrollments->take(5) as $enrollment)
<a href="{{ route('student.course.show', $enrollment->course) }}" class="sidebar-link truncate">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <span class="truncate">{{ $enrollment->course->title }}</span>
</a>
@endforeach
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome banner -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-indigo-100 mt-1">You have {{ $enrollments->count() }} enrolled courses. Keep learning!</p>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-2xl font-bold">{{ number_format(Auth::user()->points) }}</div>
                        <div class="text-xs text-indigo-100">Total Points</div>
                    </div>
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-2xl font-bold">#{{ $rank }}</div>
                        <div class="text-xs text-indigo-100">Your Rank</div>
                    </div>
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-2xl font-bold">{{ $badges->count() }}</div>
                        <div class="text-xs text-indigo-100">Badges Earned</div>
                    </div>
                </div>
            </div>
            <div class="hidden md:block text-8xl">🎓</div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalLessons = 0; $completedLessons = 0;
            foreach($enrollments as $e) {
                $totalLessons += $e->course->lessons->count();
                $completedLessons += \App\Models\UserProgress::where('user_id', Auth::id())->whereIn('lesson_id', $e->course->lessons->pluck('id'))->where('is_completed', true)->count();
            }
        @endphp
        <div class="stat-card">
            <div class="text-3xl mb-1">📚</div>
            <div class="text-2xl font-bold text-gray-900">{{ $enrollments->count() }}</div>
            <div class="text-sm text-gray-500">Enrolled Courses</div>
        </div>
        <div class="stat-card">
            <div class="text-3xl mb-1">✅</div>
            <div class="text-2xl font-bold text-gray-900">{{ $completedLessons }}</div>
            <div class="text-sm text-gray-500">Lessons Completed</div>
        </div>
        <div class="stat-card">
            <div class="text-3xl mb-1">📝</div>
            <div class="text-2xl font-bold text-gray-900">{{ $recentAttempts->count() }}</div>
            <div class="text-sm text-gray-500">Quizzes Taken</div>
        </div>
        <div class="stat-card">
            <div class="text-3xl mb-1">🏅</div>
            <div class="text-2xl font-bold text-gray-900">{{ $badges->count() }}</div>
            <div class="text-sm text-gray-500">Badges Earned</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- My Courses -->
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">My Courses</h3>
                <a href="{{ route('student.courses') }}" class="text-indigo-600 text-sm hover:underline">Browse More →</a>
            </div>
            @forelse($enrollments->take(4) as $enrollment)
            <a href="{{ route('student.course.show', $enrollment->course) }}" class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition group mb-2">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">📖</div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 truncate group-hover:text-indigo-600">{{ $enrollment->course->title }}</div>
                    <div class="text-xs text-gray-500">{{ $enrollment->course->teacher->name }}</div>
                    <div class="mt-1.5 w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-sm font-medium text-indigo-600">{{ $enrollment->progress_percent }}%</span>
                </div>
            </a>
            @empty
            <div class="text-center py-8 text-gray-400">
                <div class="text-4xl mb-2">📚</div>
                <p>No courses yet. <a href="{{ route('student.courses') }}" class="text-indigo-600 hover:underline">Browse courses</a></p>
            </div>
            @endforelse
        </div>

        <!-- Right column -->
        <div class="space-y-6">
            <!-- Top Leaderboard -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">🏆 Top Learners</h3>
                    <a href="{{ route('student.leaderboard') }}" class="text-indigo-600 text-xs hover:underline">View All</a>
                </div>
                @foreach($leaderboard as $i => $u)
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-700' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-500')) }}">{{ $i+1 }}</div>
                    <div class="flex-1 text-sm font-medium text-gray-800 truncate">{{ $u['name'] }}</div>
                    <div class="text-sm font-bold text-indigo-600">{{ number_format($u['points']) }}</div>
                </div>
                @endforeach
            </div>

            <!-- Recent Badges -->
            <div class="card p-6">
                <h3 class="font-semibold text-gray-900 mb-4">🎖️ Recent Badges</h3>
                @forelse($badges->take(4) as $badge)
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-xl">{{ $badge->icon }}</div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $badge->name }}</div>
                        <div class="text-xs text-gray-500">{{ $badge->pivot->earned_at ? \Carbon\Carbon::parse($badge->pivot->earned_at)->diffForHumans() : '' }}</div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-2">Complete lessons to earn badges!</p>
                @endforelse
                <a href="{{ route('student.badges') }}" class="text-indigo-600 text-xs hover:underline block mt-2">View All Badges →</a>
            </div>
        </div>
    </div>

    <!-- Recent Quiz Results -->
    @if($recentAttempts->count())
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">📊 Recent Quiz Results</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="pb-3 font-medium">Quiz</th>
                        <th class="pb-3 font-medium">Course</th>
                        <th class="pb-3 font-medium">Score</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttempts as $attempt)
                    <tr class="border-b border-gray-50">
                        <td class="py-3 font-medium text-gray-800">{{ $attempt->quiz->title }}</td>
                        <td class="py-3 text-gray-600">{{ $attempt->quiz->course->title }}</td>
                        <td class="py-3">
                            <span class="font-bold {{ $attempt->score_percent >= 60 ? 'text-green-600' : 'text-red-600' }}">{{ $attempt->score_percent }}%</span>
                        </td>
                        <td class="py-3">
                            <span class="badge-pill {{ $attempt->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $attempt->passed ? '✅ Passed' : '❌ Failed' }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-500">{{ $attempt->completed_at?->format('M d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
