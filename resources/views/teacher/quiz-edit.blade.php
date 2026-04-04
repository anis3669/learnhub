@extends('layouts.learnhub')
@section('title', 'Edit Quiz')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Quiz Builder: ' . $quiz->title)

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Quiz info -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-gray-900">{{ $quiz->title }}</h2>
                <p class="text-sm text-gray-500">{{ $questions->count() }} questions • {{ $quiz->time_limit_minutes }}min • Pass: {{ $quiz->passing_score }}%</p>
            </div>
            <span class="badge-pill {{ $quiz->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $quiz->is_published ? '🟢 Live' : '🟡 Draft' }}</span>
        </div>
    </div>

    <!-- Add question -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-5">➕ Add Question</h3>
        <form action="{{ route('teacher.quiz.question.add', [$course, $quiz]) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Text *</label>
                <textarea name="question_text" required rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter your question..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options (check the correct one)</label>
                <div class="space-y-2" id="options-container">
                    @foreach(range(0, 3) as $i)
                    <div class="flex items-center gap-3">
                        <input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'required' : '' }} class="w-4 h-4 text-green-600">
                        <input type="text" name="options[{{ $i }}]" placeholder="Option {{ $i+1 }}" class="flex-1 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-1">Select the radio button next to the correct answer.</p>
            </div>
            <div class="flex items-center gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                    <input type="number" name="points" value="10" min="1" class="w-24 border border-gray-300 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="flex-1 flex items-end">
                    <button type="submit" class="btn-primary">Add Question</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Existing questions -->
    @if($questions->count())
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-5">📋 Questions ({{ $questions->count() }})</h3>
        <div class="space-y-4">
            @foreach($questions as $i => $question)
            <div class="border border-gray-200 rounded-xl p-5">
                <div class="flex items-start justify-between">
                    <div class="flex gap-3">
                        <div class="w-7 h-7 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $i+1 }}</div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $question->question_text }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $question->points }} pts</p>
                        </div>
                    </div>
                    <form action="{{ route('teacher.quiz.question.delete', [$course, $quiz, $question]) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50">Delete</button>
                    </form>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-3 ml-10">
                    @foreach($question->options as $opt)
                    <div class="flex items-center gap-2 text-sm px-3 py-2 rounded-lg {{ $opt->is_correct ? 'bg-green-100 text-green-800 font-medium' : 'bg-gray-50 text-gray-600' }}">
                        {{ $opt->is_correct ? '✅' : '○' }} {{ $opt->option_text }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="card p-10 text-center text-gray-400">
        <div class="text-4xl mb-2">📝</div>
        <p>No questions yet. Add your first question above!</p>
    </div>
    @endif
</div>
@endsection
