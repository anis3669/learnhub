@extends('layouts.learnhub')
@section('title', 'Discussion')
@section('portal-name', 'Student Portal')
@section('page-title', '💬 Discussion Forum')
@section('breadcrumb', $course->title . ' → Discussion')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">← Back to Course</a>
<a href="{{ route('student.leaderboard') }}" class="sidebar-link">Leaderboard</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showForm: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Discussion: {{ $course->title }}</h2>
            <p class="text-sm text-gray-500">{{ $posts->count() }} discussions</p>
        </div>
        <button @click="showForm = !showForm" class="btn-primary">
            + New Discussion
        </button>
    </div>

    <!-- New post form -->
    <div x-show="showForm" x-transition class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Start a New Discussion</h3>
        <form action="{{ route('student.discussion.post', $course) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" required placeholder="What would you like to discuss?" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea name="body" required rows="4" placeholder="Describe your question or topic..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showForm = false" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Post Discussion (+5 pts)</button>
            </div>
        </form>
    </div>

    <!-- Posts -->
    @forelse($posts as $post)
    <div class="card overflow-hidden">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold flex-shrink-0">
                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-semibold text-gray-900">{{ $post->title }}</h3>
                        @if($post->is_pinned)<span class="badge-pill bg-yellow-100 text-yellow-700">📌 Pinned</span>@endif
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        By <span class="font-medium">{{ $post->user->name }}</span>
                        @foreach($post->user->roles as $r)<span class="badge-pill bg-gray-100 text-gray-600 ml-1">{{ $r->name }}</span>@endforeach
                        • {{ $post->created_at->diffForHumans() }}
                    </div>
                    <p class="text-gray-700 mt-3">{{ $post->body }}</p>
                </div>
            </div>
        </div>

        <!-- Replies -->
        @if($post->replies->count())
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 space-y-4">
            @foreach($post->replies as $reply)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0
                    {{ $reply->user->hasRole('teacher') ? 'bg-purple-200 text-purple-700' : 'bg-gray-200 text-gray-700' }}">
                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 bg-white rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">{{ $reply->user->name }}</span>
                        @if($reply->user->hasRole('teacher'))
                        <span class="badge-pill bg-purple-100 text-purple-700">👨‍🏫 Instructor</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-1">{{ $reply->body }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Reply form -->
        <div class="border-t border-gray-100 px-6 py-4" x-data="{ show: false }">
            <button @click="show = !show" class="text-indigo-600 text-sm hover:underline">
                💬 Reply ({{ $post->replies->count() }} replies)
            </button>
            <div x-show="show" x-transition class="mt-3">
                <form action="{{ route('student.discussion.reply', $post) }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="body" required placeholder="Write a reply..." class="flex-1 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="btn-primary">Reply (+2 pts)</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card p-12 text-center text-gray-400">
        <div class="text-5xl mb-3">💬</div>
        <p class="text-lg font-medium">No discussions yet</p>
        <p class="text-sm">Be the first to start a discussion!</p>
        <button @click="showForm = true" class="btn-primary mt-4">Start First Discussion</button>
    </div>
    @endforelse
</div>
@endsection
