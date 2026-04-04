@extends('layouts.learnhub')
@section('title', 'Quiz Result')
@section('portal-name', 'Student Portal')
@section('page-title', 'Quiz Result')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">Back to Course</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Result banner -->
    <div class="card overflow-hidden">
        <div class="{{ $attempt->passed ? 'bg-gradient-to-r from-green-400 to-emerald-600' : 'bg-gradient-to-r from-red-400 to-rose-600' }} p-10 text-white text-center">
            <div class="text-7xl mb-4">{{ $attempt->passed ? '🎉' : '📚' }}</div>
            <h2 class="text-3xl font-bold mb-2">{{ $attempt->passed ? 'Congratulations!' : 'Keep Practicing!' }}</h2>
            <p class="text-white/80">{{ $attempt->passed ? 'You passed the quiz!' : "You didn't pass this time. Review the material and try again!" }}</p>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-3 gap-6 text-center mb-6">
                <div>
                    <div class="text-4xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">{{ $attempt->score_percent }}%</div>
                    <div class="text-sm text-gray-500 mt-1">Your Score</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-800">{{ $attempt->score }}/{{ $attempt->total_points }}</div>
                    <div class="text-sm text-gray-500 mt-1">Points Earned</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-800">{{ $answers->where('is_correct', true)->count() }}/{{ $answers->count() }}</div>
                    <div class="text-sm text-gray-500 mt-1">Correct Answers</div>
                </div>
            </div>

            <!-- Progress bar -->
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Score: {{ $attempt->score_percent }}%</span>
                    <span>Pass mark: {{ $quiz->passing_score }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="{{ $attempt->passed ? 'bg-green-500' : 'bg-red-500' }} h-4 rounded-full transition-all relative" style="width: {{ $attempt->score_percent }}%">
                        <span class="absolute right-1 top-0 text-white text-xs font-bold leading-4">{{ $attempt->score_percent }}%</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-center">
                <a href="{{ route('student.quiz', [$course, $quiz]) }}" class="btn-secondary">Retake Quiz</a>
                <a href="{{ route('student.course.show', $course) }}" class="btn-primary">Back to Course</a>
                <a href="{{ route('student.leaderboard') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-600 transition">View Leaderboard 🏆</a>
            </div>
        </div>
    </div>

    <!-- Answer Review -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-5">📋 Answer Review</h3>
        <div class="space-y-5">
            @foreach($answers as $i => $answer)
            <div class="border rounded-xl p-5 {{ $answer->is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                <div class="flex items-start gap-3">
                    <span class="text-xl mt-0.5">{{ $answer->is_correct ? '✅' : '❌' }}</span>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 mb-3">{{ $i+1 }}. {{ $answer->question->question_text }}</p>
                        <div class="space-y-1.5">
                            @foreach($answer->question->options as $opt)
                            <div class="flex items-center gap-2 text-sm px-3 py-2 rounded-lg
                                {{ $opt->is_correct ? 'bg-green-100 text-green-800 font-medium' : '' }}
                                {{ $answer->selected_option_id == $opt->id && !$opt->is_correct ? 'bg-red-100 text-red-800' : '' }}
                                {{ $answer->selected_option_id == $opt->id && $opt->is_correct ? 'bg-green-200 text-green-900' : '' }}
                            ">
                                @if($opt->is_correct) ✓ @elseif($answer->selected_option_id == $opt->id) ✗ @else &nbsp;&nbsp; @endif
                                {{ $opt->option_text }}
                            </div>
                            @endforeach
                        </div>
                        @if(!$answer->is_correct)
                        <p class="text-xs text-green-700 mt-2">✅ Correct: {{ $answer->question->options->where('is_correct', true)->first()?->option_text }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
