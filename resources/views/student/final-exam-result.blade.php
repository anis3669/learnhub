@extends('layouts.learnhub')
@section('title', 'Final Exam Result')
@section('portal-name', 'Student Portal')
@section('page-title', 'Final Exam Result')
@section('breadcrumb', $course->title . ' → Final Exam Result')

@section('sidebar-nav')
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Back to Course
</a>
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Result card -->
    @if($attempt->passed)
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-8 text-white text-center shadow-lg">
        <div class="text-6xl mb-4">🎓</div>
        <h2 class="text-3xl font-extrabold mb-2">Congratulations!</h2>
        <p class="text-green-100 text-lg mb-4">You passed the Final Exam!</p>
        <div class="text-5xl font-black mb-2">{{ $attempt->score }}<span class="text-2xl text-green-200">/{{ $attempt->total_questions }}</span></div>
        <div class="text-green-100 text-sm mb-6">{{ $attempt->score_percent }}% • Passing mark: 75%</div>
        <div class="flex gap-3 justify-center flex-wrap">
            <div class="bg-white/20 rounded-xl px-5 py-3 text-center">
                <div class="text-2xl font-extrabold">+100</div>
                <div class="text-green-200 text-xs">Points Earned</div>
            </div>
            <div class="bg-white/20 rounded-xl px-5 py-3 text-center">
                <div class="text-2xl">🏆</div>
                <div class="text-green-200 text-xs">Badge Awarded</div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl p-8 text-white text-center shadow-lg">
        <div class="text-6xl mb-4">😔</div>
        <h2 class="text-3xl font-extrabold mb-2">Not Passed</h2>
        <p class="text-red-100 text-lg mb-4">Keep practising and try again!</p>
        <div class="text-5xl font-black mb-2">{{ $attempt->score }}<span class="text-2xl text-red-200">/{{ $attempt->total_questions }}</span></div>
        <div class="text-red-100 text-sm mb-6">{{ $attempt->score_percent }}% • Need 75% (15/20) to pass</div>
        <a href="{{ route('student.final-exam', $course) }}" class="inline-block bg-white text-red-600 font-bold px-8 py-3 rounded-xl hover:bg-red-50 transition">
            Try Again →
        </a>
    </div>
    @endif

    <!-- Answer Review -->
    <div class="card p-6">
        <h3 class="font-bold text-gray-900 text-lg mb-5">📋 Answer Review</h3>
        <div class="space-y-4">
            @foreach($attempt->answers as $i => $answer)
            @php $q = $answer->question; @endphp
            <div class="rounded-xl border-2 p-4 {{ $answer->is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-extrabold flex-shrink-0
                        {{ $answer->is_correct ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $i + 1 }}
                    </div>
                    <p class="font-medium text-gray-900 text-sm">{{ $q->question_text }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 ml-10">
                    @foreach([1 => $q->option_a, 2 => $q->option_b, 3 => $q->option_c, 4 => $q->option_d] as $num => $opt)
                    <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs
                        {{ $num == $q->correct_option ? 'bg-green-500 text-white font-bold' :
                           ($answer->selected_option == $num && !$answer->is_correct ? 'bg-red-400 text-white' : 'bg-white text-gray-600 border border-gray-200') }}">
                        <span class="font-bold">{{ chr(64 + $num) }}.</span>
                        <span>{{ $opt }}</span>
                        @if($num == $q->correct_option)
                        <span class="ml-auto">✓</span>
                        @elseif($answer->selected_option == $num && !$answer->is_correct)
                        <span class="ml-auto">✗</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if(!$answer->is_correct && !$answer->selected_option)
                <div class="ml-10 mt-2 text-xs text-gray-400 italic">Not answered</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Previous attempts -->
    @if($allAttempts->count() > 1)
    <div class="card p-6">
        <h3 class="font-bold text-gray-900 mb-4">📊 Attempt History</h3>
        <div class="space-y-2">
            @foreach($allAttempts as $i => $att)
            <div class="flex items-center justify-between p-3 rounded-xl {{ $att->id === $attempt->id ? 'bg-indigo-50 border border-indigo-200' : 'bg-gray-50' }}">
                <div class="text-sm">
                    <span class="font-medium">Attempt #{{ $allAttempts->count() - $i }}</span>
                    <span class="text-gray-400 ml-2">{{ $att->completed_at?->format('M j, Y g:i A') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-sm">{{ $att->score }}/{{ $att->total_questions }}</span>
                    <span class="badge-pill {{ $att->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $att->passed ? '✅ Passed' : '❌ Failed' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex gap-4">
        <a href="{{ route('student.course.show', $course) }}" class="btn-secondary flex-1 text-center">← Back to Course</a>
        <a href="{{ route('student.dashboard') }}" class="btn-primary flex-1 text-center">Go to Dashboard →</a>
    </div>
</div>
@endsection
