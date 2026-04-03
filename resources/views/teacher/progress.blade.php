@extends('layouts.learnhub')
@section('title', 'Student Progress')
@section('portal-name', 'Teacher Portal')
@section('page-title', 'Student Progress')
@section('breadcrumb', $course->title . ' → Progress')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="card p-4 bg-purple-50 border-purple-100">
        <p class="text-sm text-purple-800">📊 Showing progress for <strong>{{ $course->title }}</strong> — {{ $progressData->count() }} enrolled students</p>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Student</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Lessons ({{ $lessons->count() }})</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Progress</th>
                    @foreach($quizzes as $quiz)
                    <th class="text-left px-6 py-4 font-medium text-gray-600">{{ Str::limit($quiz->title, 20) }}</th>
                    @endforeach
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Enrolled</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($progressData as $pd)
                @php $e = $pd['enrollment']; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                {{ strtoupper(substr($e->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $e->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $e->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium">{{ $pd['completedLessons'] }}</span>
                        <span class="text-gray-400">/{{ $lessons->count() }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $e->progress_percent }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-indigo-600">{{ $e->progress_percent }}%</span>
                        </div>
                    </td>
                    @foreach($quizzes as $quiz)
                    @php $qa = $pd['quizAttempts']->where('quiz_id', $quiz->id)->first(); @endphp
                    <td class="px-6 py-4">
                        @if($qa)
                        <span class="badge-pill {{ $qa->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $qa->score_percent }}% {{ $qa->passed ? '✅' : '❌' }}
                        </span>
                        @else
                        <span class="text-gray-400 text-xs">Not taken</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $e->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
