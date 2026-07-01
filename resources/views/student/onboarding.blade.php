@extends('layouts.learnhub')

@section('title', 'Skills Assessment')
@section('portal-name', 'Student Portal')
@section('page-title', 'Programming Assessment')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Back to Dashboard
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8" x-data="{
        current: 1,
        total: {{ count($questions) }},
        answers: {},
        submitted: false,
        setAnswer(qId, optIdx) { this.answers[qId] = optIdx; },
        hasAnswer(qId) { return this.answers[qId] !== undefined; },
        get progress() { return Math.round((this.current / this.total) * 100); },
        get allAnswered() { return Object.keys(this.answers).length === this.total; }
    }">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="text-4xl mb-3">🧠</div>
            <h2 class="text-2xl font-bold text-gray-900">Programming Skills Assessment</h2>
            <p class="text-gray-500 mt-2">10 questions • ~5 minutes • Determines your learning path</p>
        </div>

        {{-- Progress bar --}}
        <div class="mb-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Question <span x-text="current"></span> of {{ count($questions) }}</span>
                <span x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>

        <form action="{{ route('student.assessment.submit') }}" method="POST" id="assessmentForm">
            @csrf

            @foreach($questions as $q)
            <div x-show="current === {{ $loop->iteration }}" x-transition>
                <div class="mb-6">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $loop->iteration }}</span>
                        <p class="text-lg font-semibold text-gray-900">{{ $q['text'] }}</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($q['options'] as $idx => $option)
                        <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                               :class="answers[{{ $q['id'] }}] === {{ $idx }} ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50'"
                               @click="setAnswer({{ $q['id'] }}, {{ $idx }})">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition"
                                 :class="answers[{{ $q['id'] }}] === {{ $idx }} ? 'border-indigo-600' : 'border-gray-300'">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-600" x-show="answers[{{ $q['id'] }}] === {{ $idx }}"></div>
                            </div>
                            <input type="radio" name="answers[{{ $q['id'] }}]" value="{{ $idx }}" class="hidden"
                                   :checked="answers[{{ $q['id'] }}] === {{ $idx }}">
                            <span class="text-gray-800">{{ $option }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="current = Math.max(1, current - 1)"
                            class="btn-secondary" x-show="current > 1">← Previous</button>
                    <div x-show="current === 1"></div>

                    <button type="button" @click="current = Math.min(total, current + 1)"
                            class="btn-primary" x-show="current < total"
                            :disabled="!hasAnswer({{ $q['id'] }})"
                            :class="!hasAnswer({{ $q['id'] }}) ? 'opacity-50 cursor-not-allowed' : ''">
                        Next →
                    </button>

                    @if($loop->last)
                    <button type="submit" x-show="current === total"
                            :disabled="!allAnswered"
                            :class="!allAnswered ? 'opacity-50 cursor-not-allowed' : ''"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-green-700 transition">
                        Submit Assessment 🎯
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </form>

        {{-- Question nav dots --}}
        <div class="flex justify-center gap-2 mt-6 flex-wrap">
            @foreach($questions as $q)
            <button type="button" @click="current = {{ $loop->iteration }}"
                    class="w-8 h-8 rounded-full text-xs font-medium transition"
                    :class="current === {{ $loop->iteration }} ? 'bg-indigo-600 text-white' : (answers[{{ $q['id'] }}] !== undefined ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600')">
                {{ $loop->iteration }}
            </button>
            @endforeach
        </div>
    </div>

    <div class="text-center mt-4">
        <p class="text-sm text-gray-500">💡 Answer all questions and click "Submit Assessment" to get your personalised learning path.</p>
    </div>
</div>
@endsection
