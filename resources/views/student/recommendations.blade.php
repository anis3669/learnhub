@extends('layouts.learnhub')
@section('title', 'Course Recommendations')
@section('portal-name', 'Student Portal')
@section('page-title', 'Your Course Recommendations')
@section('breadcrumb', 'Skill Assessment → Recommendations')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.courses') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Browse All Courses
</a>
<a href="{{ route('student.skill-assessment') }}" class="sidebar-link">
    🔄 Retake Assessment
</a>
<a href="{{ route('student.recommendations') }}" class="sidebar-link active">
    ✨ My Recommendations
</a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Score card -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="text-indigo-200 text-sm font-medium uppercase tracking-wider mb-2">Assessment Result</div>
                <h2 class="text-3xl font-extrabold mb-1">{{ $lastAssessment->score }} / {{ $lastAssessment->total_questions }} Correct</h2>
                <p class="text-indigo-200">{{ round(($lastAssessment->score / $lastAssessment->total_questions) * 100) }}% score • Taken {{ $lastAssessment->created_at->diffForHumans() }}</p>
            </div>
            <div class="text-center">
                <div class="w-28 h-28 rounded-full border-4 border-white/30 flex flex-col items-center justify-center bg-white/10 backdrop-blur">
                    <div class="text-3xl font-extrabold">{{ $level[0] }}</div>
                    <div class="text-xs text-indigo-200 mt-1 font-medium">{{ $level }}</div>
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-3 gap-4 text-center">
            @foreach(['Beginner' => ['0–3', 'bg-green-500'], 'Intermediate' => ['4–7', 'bg-yellow-400'], 'Advanced' => ['8–10', 'bg-red-500']] as $lvl => [$range, $color])
            <div class="rounded-xl p-3 {{ $lvl === $level ? 'bg-white/20 ring-2 ring-white' : 'bg-white/10' }}">
                <div class="text-sm font-bold {{ $lvl === $level ? 'text-white' : 'text-indigo-200' }}">{{ $lvl }}</div>
                <div class="text-xs text-indigo-300">{{ $range }} correct</div>
                @if($lvl === $level)
                <div class="text-xs mt-1 font-bold text-yellow-300">← YOUR LEVEL</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recommended courses -->
    @if($recommended->isNotEmpty())
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">✨</div>
            <h3 class="text-lg font-bold text-gray-900">Recommended for You — {{ $level }} Level</h3>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommended as $course)
            <div class="card overflow-hidden hover:shadow-md transition-shadow ring-2 ring-indigo-200">
                <div class="h-36 bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-5xl relative">
                    @php $icons = ['Programming'=>'💻','Web Development'=>'🌐','Computer Science'=>'🔬','AI & ML'=>'🤖','General'=>'📚']; @endphp
                    {{ $icons[$course->category] ?? '📖' }}
                    <div class="absolute top-2 right-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full font-bold">Recommended ✨</div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge-pill bg-indigo-100 text-indigo-700">{{ $course->category }}</span>
                        <span class="badge-pill bg-green-100 text-green-700">{{ $course->level }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $course->title }}</h3>
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $course->description }}</p>
                    <div class="flex justify-between text-xs text-gray-500 mb-4">
                        <span>👨‍🏫 {{ $course->teacher->name }}</span>
                        <span>{{ $course->lessons_count }} lessons</span>
                    </div>
                    @if(in_array($course->id, $enrolledIds))
                        <a href="{{ route('student.course.show', $course) }}" class="block text-center btn-primary w-full">Continue →</a>
                    @else
                        <form action="{{ route('student.enroll', $course) }}" method="POST">
                            @csrf
                            <button class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Enroll Now →</button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="card p-8 text-center">
        <div class="text-4xl mb-3">🔍</div>
        <p class="text-gray-500">No {{ $level }} level courses available right now.</p>
    </div>
    @endif

    <!-- Other courses -->
    @if($otherCourses->isNotEmpty())
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">📚</div>
            <h3 class="text-lg font-bold text-gray-900">Other Available Courses</h3>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($otherCourses as $course)
            <div class="card overflow-hidden hover:shadow-md transition-shadow opacity-80 hover:opacity-100">
                <div class="h-28 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-4xl">
                    {{ $icons[$course->category] ?? '📖' }}
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge-pill bg-gray-100 text-gray-600">{{ $course->level }}</span>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-1 text-sm">{{ $course->title }}</h3>
                    <div class="flex justify-between text-xs text-gray-400 mb-3">
                        <span>{{ $course->lessons_count }} lessons</span>
                        <span>👥 {{ $course->enrollments_count }}</span>
                    </div>
                    @if(in_array($course->id, $enrolledIds))
                        <a href="{{ route('student.course.show', $course) }}" class="block text-center btn-secondary text-xs w-full">Continue</a>
                    @else
                        <form action="{{ route('student.enroll', $course) }}" method="POST">
                            @csrf
                            <button class="w-full border border-gray-300 text-gray-600 px-4 py-1.5 rounded-lg text-xs hover:bg-gray-50 transition">Enroll</button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="text-center">
        <a href="{{ route('student.skill-assessment') }}" class="btn-secondary inline-flex items-center gap-2">
            🔄 Retake Assessment
        </a>
    </div>
</div>
@endsection
