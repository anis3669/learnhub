@extends('layouts.learnhub')
@section('title', $lesson->title)
@section('portal-name', 'Student Portal')
@section('page-title', $lesson->title)
@section('breadcrumb', $course->title . ' → ' . $lesson->title)

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Back to Course
</a>
<div class="pt-2 pb-1 px-1 text-xs text-gray-400 uppercase tracking-wider">Course Lessons</div>
@foreach($course->lessons as $l)
<a href="{{ route('student.lesson', [$course, $l]) }}" class="sidebar-link text-xs {{ $l->id === $lesson->id ? 'active' : '' }}">
    @php $p = \App\Models\UserProgress::where('user_id', Auth::id())->where('lesson_id', $l->id)->first(); @endphp
    {{ $p?->is_completed ? '✅' : ($l->id === $lesson->id ? '▶️' : '○') }}
    <span class="truncate ml-1">{{ $l->title }}</span>
</a>
@endforeach
@endsection

@section('content')
<div class="space-y-6">
    <!-- Video Player -->
    <div class="card overflow-hidden">
        @if($lesson->video_url)
        <div class="relative w-full" style="padding-top: 56.25%;">
            <iframe
                src="{{ $lesson->embed_url }}"
                class="absolute inset-0 w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>
        @else
        <div class="h-64 bg-gray-900 flex items-center justify-center text-white">
            <div class="text-center">
                <div class="text-5xl mb-3">📹</div>
                <p class="text-gray-300">No video for this lesson yet</p>
            </div>
        </div>
        @endif

        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $lesson->title }}</h2>
                    @if($lesson->description)
                    <p class="text-gray-600 mt-1">{{ $lesson->description }}</p>
                    @endif
                    <div class="text-xs text-gray-400 mt-1">⏱ {{ $lesson->duration_minutes }} minutes</div>
                </div>
                @if(!$userProgress->is_completed)
                <form action="{{ route('student.lesson.complete', [$course, $lesson]) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2">
                        ✅ Mark as Complete (+20 pts)
                    </button>
                </form>
                @else
                <div class="bg-green-100 text-green-700 px-6 py-2.5 rounded-xl font-medium flex items-center gap-2">
                    ✅ Lesson Completed!
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Content -->
    @if($lesson->content)
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-3">📝 Lesson Notes</h3>
        <div class="prose text-gray-700">{{ $lesson->content }}</div>
    </div>
    @endif

    <!-- Quiz CTA -->
    @if($quiz)
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg">🧠 Ready to Test Your Knowledge?</h3>
                <p class="text-purple-100 mt-1">{{ $quiz->title }} • {{ $quiz->time_limit_minutes }}min • Pass: {{ $quiz->passing_score }}%</p>
            </div>
            <a href="{{ route('student.quiz', [$course, $quiz]) }}" class="bg-white text-purple-700 px-6 py-2.5 rounded-xl font-bold hover:bg-purple-50 transition">
                Take Quiz →
            </a>
        </div>
    </div>
    @endif

    <!-- Navigation -->
    <div class="flex items-center justify-between">
        @if($prev)
        <a href="{{ route('student.lesson', [$course, $prev]) }}" class="btn-secondary flex items-center gap-2">
            ← Previous: {{ Str::limit($prev->title, 30) }}
        </a>
        @else
        <div></div>
        @endif
        @if($next)
        <a href="{{ route('student.lesson', [$course, $next]) }}" class="btn-primary flex items-center gap-2">
            Next: {{ Str::limit($next->title, 30) }} →
        </a>
        @else
        <a href="{{ route('student.course.show', $course) }}" class="btn-primary">
            Back to Course →
        </a>
        @endif
    </div>
</div>
@endsection
