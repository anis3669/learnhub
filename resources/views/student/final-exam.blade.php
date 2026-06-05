@extends('layouts.learnhub')
@section('title', 'Final Exam — ' . $course->title)
@section('portal-name', 'Student Portal')
@section('page-title', 'Final Exam')
@section('breadcrumb', $course->title . ' → Final Exam')

@section('sidebar-nav')
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Back to Course
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6"
    x-data="{
        current: 1,
        total: {{ $questions->count() }},
        answers: {},
        timeLeft: 3600,
        timerInterval: null,
        formatTime(s) {
            let m = Math.floor(s/60);
            let sec = s % 60;
            return String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
        },
        startTimer() {
            this.timerInterval = setInterval(() => {
                if(this.timeLeft > 0) { this.timeLeft--; }
                else { clearInterval(this.timerInterval); document.getElementById('exam-form').submit(); }
            }, 1000);
        }
    }"
    x-init="startTimer()">

    <!-- Exam header -->
    <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-red-200 text-sm font-medium uppercase tracking-wider">Final Course Examination</div>
                <h2 class="text-2xl font-extrabold mt-1">{{ $course->title }}</h2>
                <p class="text-red-200 text-sm mt-1">20 questions • Pass 15/20 (75%) to earn completion status</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 rounded-2xl bg-white/10 flex flex-col items-center justify-center border-2 border-white/30">
                    <div class="text-xs text-red-200 font-medium">TIME LEFT</div>
                    <div class="text-xl font-black" x-text="formatTime(timeLeft)"></div>
                </div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-3 text-center text-sm">
            <div class="bg-white/10 rounded-xl p-2">
                <div class="font-bold" x-text="Object.keys(answers).length"></div>
                <div class="text-red-200 text-xs">Answered</div>
            </div>
            <div class="bg-white/10 rounded-xl p-2">
                <div class="font-bold" x-text="total - Object.keys(answers).length"></div>
                <div class="text-red-200 text-xs">Remaining</div>
            </div>
            <div class="bg-white/10 rounded-xl p-2">
                <div class="font-bold">15</div>
                <div class="text-red-200 text-xs">Pass Mark</div>
            </div>
        </div>
    </div>

    <!-- Question navigator -->
    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-3 font-medium">QUESTION NAVIGATOR</div>
        <div class="flex flex-wrap gap-2">
            @foreach($questions as $i => $q)
            <button type="button"
                class="w-9 h-9 rounded-lg text-sm font-bold transition border-2"
                :class="answers['{{ $q->id }}'] ? 'bg-indigo-600 text-white border-indigo-600' : (current == {{ $i+1 }} ? 'border-indigo-400 text-indigo-600' : 'border-gray-200 text-gray-500 hover:border-indigo-300')"
                @click="current = {{ $i+1 }}; document.getElementById('q-{{ $i+1 }}').scrollIntoView({behavior:'smooth',block:'center'})">
                {{ $i + 1 }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Questions -->
    <form id="exam-form" action="{{ route('student.final-exam.submit', [$course, $attempt]) }}" method="POST">
        @csrf
        <div class="space-y-4">
            @foreach($questions as $i => $q)
            <div class="card p-6 scroll-mt-4 transition-all" id="q-{{ $i+1 }}"
                :class="current == {{ $i+1 }} ? 'ring-2 ring-indigo-300' : ''">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-extrabold flex-shrink-0"
                        :class="answers['{{ $q->id }}'] ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600'">
                        {{ $i + 1 }}
                    </div>
                    <p class="font-medium text-gray-900 pt-1">{{ $q->question_text }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 ml-12">
                    @foreach([1 => $q->option_a, 2 => $q->option_b, 3 => $q->option_c, 4 => $q->option_d] as $num => $option)
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition hover:border-indigo-300 hover:bg-indigo-50"
                        :class="answers['{{ $q->id }}'] == '{{ $num }}' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $num }}"
                            class="hidden"
                            x-model="answers['{{ $q->id }}']"
                            @change="if(current < total) current = {{ $i + 2 }}">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition"
                            :class="answers['{{ $q->id }}'] == '{{ $num }}' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                            <div class="w-2 h-2 rounded-full bg-white" x-show="answers['{{ $q->id }}'] == '{{ $num }}'"></div>
                        </div>
                        <span class="text-sm text-gray-700"><strong class="text-gray-400">{{ chr(64 + $num) }}.</strong> {{ $option }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="font-semibold text-gray-900">Ready to Submit?</div>
                    <div class="text-sm text-gray-500 mt-1">
                        You have answered <span class="font-bold text-indigo-600" x-text="Object.keys(answers).length"></span> of {{ $questions->count() }} questions.
                        <span x-show="Object.keys(answers).length < {{ $questions->count() }}" class="text-orange-500">⚠ Unanswered questions will be marked wrong.</span>
                    </div>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white py-4 rounded-xl font-bold text-base transition shadow-sm">
                🎯 Submit Final Exam
            </button>
            <p class="text-center text-xs text-gray-400 mt-3">This action cannot be undone. You need 15/20 correct answers to pass.</p>
        </div>
    </form>
</div>
@endsection
