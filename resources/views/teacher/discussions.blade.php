@extends('layouts.learnhub')
@section('title', 'Student Discussions')
@section('portal-name', 'Teacher Portal')
@section('page-title', '💬 Student Discussions')

@section('sidebar-nav')
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('teacher.courses') }}" class="sidebar-link">My Courses</a>
<a href="{{ route('teacher.discussions') }}" class="sidebar-link active">Discussions</a>
@endsection

@section('content')
<div class="space-y-4">
    @forelse($posts as $post)
    <div class="card p-6">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold text-gray-900">{{ $post->title }}</h3>
                    <span class="badge-pill bg-blue-100 text-blue-700">{{ $post->course->title }}</span>
                    @if($post->is_pinned)<span class="badge-pill bg-yellow-100 text-yellow-700">📌 Pinned</span>@endif
                </div>
                <p class="text-sm text-gray-500">By <strong>{{ $post->user->name }}</strong> • {{ $post->created_at->diffForHumans() }}</p>
            </div>
            <span class="badge-pill bg-gray-100 text-gray-600">{{ $post->replies->count() }} replies</span>
        </div>
        <p class="text-gray-700 text-sm mb-4">{{ $post->body }}</p>

        @if($post->replies->count())
        <div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-3">
            @foreach($post->replies->take(3) as $reply)
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                    {{ $reply->user->hasRole('teacher') ? 'bg-purple-200 text-purple-700' : 'bg-gray-200 text-gray-700' }}">
                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                </div>
                <div class="bg-white rounded-lg px-3 py-2 shadow-sm flex-1">
                    <div class="text-xs font-medium text-gray-700">{{ $reply->user->name }} @if($reply->user->hasRole('teacher'))<span class="badge-pill bg-purple-100 text-purple-600 ml-1">Instructor</span>@endif</div>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $reply->body }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('teacher.discussion.reply', $post) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="body" required placeholder="Write a reply as instructor..." class="flex-1 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button class="bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-purple-700 transition">Reply as Instructor</button>
        </form>
    </div>
    @empty
    <div class="card p-16 text-center text-gray-400">
        <div class="text-5xl mb-3">💬</div>
        <p class="text-lg font-medium">No discussions yet</p>
        <p class="text-sm">Student discussions will appear here</p>
    </div>
    @endforelse
    {{ $posts->links() }}
</div>
@endsection
