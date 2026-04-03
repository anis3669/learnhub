@extends('layouts.learnhub')
@section('title', $course->title)
@section('portal-name', 'Student Portal')
@section('page-title', $course->title)
@section('breadcrumb', 'My Courses → ' . $course->title)

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.courses') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Browse Courses
</a>
<div class="pt-2 pb-1 px-1 text-xs text-gray-400 uppercase tracking-wider">This Course</div>
@foreach($lessons as $lesson)
<a href="{{ route('student.lesson', [$course, $lesson]) }}" class="sidebar-link text-xs {{ optional($lesson->progress->first())->is_completed ? 'text-green-600' : '' }}">
    @if(optional($lesson->progress->first())->is_completed)
        ✅
    @else
        <span class="w-4 h-4 border-2 border-gray-300 rounded-full inline-block mr-2 flex-shrink-0"></span>
    @endif
    <span class="truncate">{{ $lesson->title }}</span>
</a>
@endforeach
<a href="{{ route('student.discussion', $course) }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    Discussion
</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <!-- Main content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Course info -->
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge-pill bg-indigo-100 text-indigo-700">{{ $course->category }}</span>
                        <span class="badge-pill bg-gray-100 text-gray-700">{{ $course->level }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $course->title }}</h2>
                    <p class="text-gray-600 mt-1">By {{ $course->teacher->name }}</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">{{ $course->description }}</p>
            <!-- Progress -->
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-medium text-gray-700">Course Progress</span>
                    <span class="font-bold text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-indigo-500 h-3 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-2">{{ $lessons->where('progress.0.is_completed', true)->count() }} of {{ $lessons->count() }} lessons completed</div>
            </div>
        </div>

        <!-- Lessons -->
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">📹 Course Lessons</h3>
            <div class="space-y-2">
                @foreach($lessons as $i => $lesson)
                @php $completed = optional($lesson->progress->first())->is_completed; @endphp
                <a href="{{ route('student.lesson', [$course, $lesson]) }}" class="flex items-center space-x-4 p-4 rounded-xl border {{ $completed ? 'border-green-200 bg-green-50' : 'border-gray-100 hover:border-indigo-200 hover:bg-indigo-50' }} transition group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0 {{ $completed ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-indigo-500 group-hover:text-white' }}">
                        {{ $completed ? '✓' : $i+1 }}
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 group-hover:text-indigo-700">{{ $lesson->title }}</div>
                        @if($lesson->description)
                        <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($lesson->description, 80) }}</div>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 flex-shrink-0">{{ $lesson->duration_minutes }}min</div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Quizzes -->
        @if($quizzes->count())
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">🧠 Course Quizzes</h3>
            @foreach($quizzes as $quiz)
            @php $latestAttempt = $quiz->attempts->first(); @endphp
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-indigo-200 transition mb-2">
                <div>
                    <div class="font-medium text-gray-900">{{ $quiz->title }}</div>
                    <div class="text-xs text-gray-500 mt-1">⏱ {{ $quiz->time_limit_minutes }}min • Pass: {{ $quiz->passing_score }}%</div>
                    @if($latestAttempt)
                    <span class="badge-pill mt-1 {{ $latestAttempt->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        Last: {{ $latestAttempt->score_percent }}% {{ $latestAttempt->passed ? '✅' : '❌' }}
                    </span>
                    @endif
                </div>
                <a href="{{ route('student.quiz', [$course, $quiz]) }}" class="btn-primary">
                    {{ $latestAttempt ? 'Retake' : 'Take Quiz' }}
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Discussion preview -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">💬 Discussion</h3>
                <a href="{{ route('student.discussion', $course) }}" class="text-indigo-600 text-xs hover:underline">View All</a>
            </div>
            @forelse($posts as $post)
            <div class="mb-3 pb-3 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $post->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">By {{ $post->user->name }} • {{ $post->replies->count() }} replies</p>
            </div>
            @empty
            <p class="text-sm text-gray-400">No discussions yet.</p>
            @endforelse
            <a href="{{ route('student.discussion', $course) }}" class="block mt-3 text-center btn-secondary text-sm">Start a Discussion</a>
        </div>
    </div>
</div>
@endsection
