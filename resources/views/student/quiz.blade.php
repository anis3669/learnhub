@extends('layouts.learnhub')
@section('title', $quiz->title)
@section('portal-name', 'Student Portal')
@section('page-title', $quiz->title)
@section('breadcrumb', $course->title . ' → Quiz')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Quiz header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-bold">🧠 {{ $quiz->title }}</h2>
        @if($quiz->description)
        <p class="text-purple-100 mt-1">{{ $quiz->description }}</p>
        @endif
        <div class="flex items-center gap-6 mt-4 text-sm">
            <span>⏱ {{ $quiz->time_limit_minutes }} minutes</span>
            <span>📝 {{ $questions->count() }} questions</span>
            <span>🎯 Pass: {{ $quiz->passing_score }}%</span>
            <span>⭐ {{ $questions->sum('points') }} total points</span>
        </div>
    </div>

    <!-- Timer -->
    <div class="card p-4 flex items-center justify-between" x-data="quizTimer({{ $quiz->time_limit_minutes * 60 }})" x-init="start()">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">⏱</div>
            <div>
                <div class="text-xs text-gray-500">Time Remaining</div>
                <div class="text-xl font-bold" :class="seconds < 60 ? 'text-red-600' : 'text-gray-900'" x-text="formatTime()"></div>
            </div>
        </div>
        <div class="text-sm text-gray-500">Question <span id="current-q">1</span> / {{ $questions->count() }}</div>
    </div>

    <!-- Questions -->
    <form action="{{ route('student.quiz.submit', [$course, $quiz]) }}" method="POST" id="quizForm">
        @csrf
        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

        @foreach($questions as $i => $question)
        <div class="card p-6 question-block" id="question-{{ $i+1 }}" style="{{ $i > 0 ? 'display:none' : '' }}">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">{{ $i+1 }}</div>
                <p class="text-gray-900 font-medium text-lg">{{ $question->question_text }}</p>
            </div>
            <div class="space-y-3 ml-12">
                @foreach($question->options as $option)
                <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-100 hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer transition group has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="w-4 h-4 text-indigo-600">
                    <span class="text-gray-800 group-has-[:checked]:font-medium">{{ $option->option_text }}</span>
                </label>
                @endforeach
            </div>
            <div class="flex justify-between mt-6 ml-12">
                @if($i > 0)
                <button type="button" onclick="prevQuestion({{ $i+1 }})" class="btn-secondary">← Previous</button>
                @else
                <div></div>
                @endif
                @if($i < $questions->count() - 1)
                <button type="button" onclick="nextQuestion({{ $i+1 }})" class="btn-primary">Next →</button>
                @else
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-green-700 transition">Submit Quiz ✅</button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>
@endsection

@push('scripts')
<script>
function quizTimer(totalSeconds) {
    return {
        seconds: totalSeconds,
        interval: null,
        start() {
            this.interval = setInterval(() => {
                this.seconds--;
                if (this.seconds <= 0) {
                    clearInterval(this.interval);
                    document.getElementById('quizForm').submit();
                }
            }, 1000);
        },
        formatTime() {
            const m = Math.floor(this.seconds / 60).toString().padStart(2, '0');
            const s = (this.seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        }
    }
}

function nextQuestion(current) {
    document.getElementById('question-' + current).style.display = 'none';
    document.getElementById('question-' + (current + 1)).style.display = 'block';
    document.getElementById('current-q').textContent = current + 1;
}

function prevQuestion(current) {
    document.getElementById('question-' + current).style.display = 'none';
    document.getElementById('question-' + (current - 1)).style.display = 'block';
    document.getElementById('current-q').textContent = current - 1;
}
</script>
@endpush
