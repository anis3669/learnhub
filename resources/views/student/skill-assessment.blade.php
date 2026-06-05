@extends('layouts.learnhub')
@section('title', 'Skill Assessment')
@section('portal-name', 'Student Portal')
@section('page-title', 'Programming Skill Assessment')
@section('breadcrumb', 'Skill Assessment')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.courses') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Browse Courses
</a>
<a href="{{ route('student.skill-assessment') }}" class="sidebar-link active">
    🧩 Skill Assessment
</a>
@if($lastAssessment)
<a href="{{ route('student.recommendations') }}" class="sidebar-link">
    ✨ My Recommendations
</a>
@endif
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ current: 1, total: {{ $questions->count() }}, answers: {} }">

    @if($lastAssessment)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-center justify-between">
        <div>
            <div class="font-semibold text-blue-800">Previous Assessment Result</div>
            <div class="text-sm text-blue-600 mt-1">
                Score: <strong>{{ $lastAssessment->score }}/{{ $lastAssessment->total_questions }}</strong> •
                Recommended: <strong>{{ $lastAssessment->recommended_level }}</strong> •
                Taken {{ $lastAssessment->created_at->diffForHumans() }}
            </div>
        </div>
        <a href="{{ route('student.recommendations') }}" class="btn-primary text-sm">View Recommendations →</a>
    </div>
    @endif

    <!-- Header -->
    <div class="card p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-2xl">🧩</div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Programming Skill Assessment</h2>
                <p class="text-gray-500 text-sm">{{ $questions->count() }} questions • ~5 minutes • Recommends the right course level for you</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="bg-green-50 rounded-xl p-3">
                <div class="text-lg font-bold text-green-700">0–3</div>
                <div class="text-xs text-green-600">Beginner Courses</div>
            </div>
            <div class="bg-yellow-50 rounded-xl p-3">
                <div class="text-lg font-bold text-yellow-700">4–7</div>
                <div class="text-xs text-yellow-600">Intermediate Courses</div>
            </div>
            <div class="bg-red-50 rounded-xl p-3">
                <div class="text-lg font-bold text-red-700">8–10</div>
                <div class="text-xs text-red-600">Advanced Courses</div>
            </div>
        </div>
    </div>

    <!-- Progress bar -->
    <div class="card p-4">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-600">Question <span x-text="current"></span> of {{ $questions->count() }}</span>
            <span class="font-medium text-indigo-600" x-text="Math.round((Object.keys(answers).length / {{ $questions->count() }}) * 100) + '% answered'"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-indigo-500 h-2 rounded-full transition-all" :style="'width: ' + Math.round((Object.keys(answers).length / {{ $questions->count() }}) * 100) + '%'"></div>
        </div>
    </div>

    <!-- Questions form -->
    <form action="{{ route('student.skill-assessment.submit') }}" method="POST">
        @csrf
        <div class="space-y-4">
            @foreach($questions as $i => $q)
            <div class="card p-6" id="q-{{ $i+1 }}">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0
                        {{ $q->difficulty === 'basic' ? 'bg-green-100 text-green-700' : ($q->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $q->difficulty === 'basic' ? 'bg-green-100 text-green-700' : ($q->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($q->difficulty) }}
                            </span>
                            @if($q->topic)
                            <span class="text-xs text-gray-400">{{ $q->topic }}</span>
                            @endif
                        </div>
                        <p class="font-medium text-gray-900">{{ $q->question_text }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 ml-11">
                    @foreach(['a' => $q->option_a, 'b' => $q->option_b, 'c' => $q->option_c, 'd' => $q->option_d] as $key => $option)
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition
                        hover:border-indigo-300 hover:bg-indigo-50"
                        :class="answers['{{ $q->id }}'] === '{{ $key }}' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}"
                            class="text-indigo-600 hidden"
                            x-model="answers['{{ $q->id }}']"
                            @change="if(current < total) { current = Math.max(current, {{ $i+2 }}) }">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition"
                            :class="answers['{{ $q->id }}'] === '{{ $key }}' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                            <div class="w-2 h-2 rounded-full bg-white" x-show="answers['{{ $q->id }}'] === '{{ $key }}'"></div>
                        </div>
                        <span class="text-sm text-gray-700"><strong class="text-gray-500">{{ strtoupper($key) }}.</strong> {{ $option }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            <button type="submit"
                class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition shadow-sm"
                :class="Object.keys(answers).length < {{ $questions->count() }} ? 'opacity-60 cursor-not-allowed' : ''"
                :disabled="Object.keys(answers).length < {{ $questions->count() }}">
                <span x-show="Object.keys(answers).length < {{ $questions->count() }}">
                    Answer all questions to submit ({{ $questions->count() }} required)
                </span>
                <span x-show="Object.keys(answers).length >= {{ $questions->count() }}">
                    Submit Assessment → Get Course Recommendations
                </span>
            </button>
            <p class="text-center text-xs text-gray-400 mt-3">Your answers are evaluated to recommend the best course level for you.</p>
        </div>
    </form>
</div>
@endsection
