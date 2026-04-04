@extends('layouts.learnhub')
@section('title', 'Browse Courses')
@section('portal-name', 'Student Portal')
@section('page-title', 'Browse Courses')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.courses') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Browse Courses
</a>
<a href="{{ route('student.leaderboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    Leaderboard
</a>
<a href="{{ route('student.badges') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
    My Badges
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <form method="GET" class="card p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <select name="category" class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <select name="level" class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Levels</option>
                @foreach(['Beginner', 'Intermediate', 'Advanced'] as $l)
                <option value="{{ $l }}" {{ request('level') == $l ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Search</button>
            @if(request()->hasAny(['search','category','level']))
            <a href="{{ route('student.courses') }}" class="btn-secondary">Clear</a>
            @endif
        </div>
    </form>

    <!-- Courses grid -->
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($courses as $course)
        <div class="card overflow-hidden hover:shadow-md transition-shadow">
            <div class="h-40 bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-5xl">
                @php $icons = ['Programming'=>'💻','Web Development'=>'🌐','Computer Science'=>'🔬','AI & ML'=>'🤖','General'=>'📚']; @endphp
                {{ $icons[$course->category] ?? '📖' }}
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge-pill bg-indigo-100 text-indigo-700">{{ $course->category }}</span>
                    <span class="badge-pill {{ $course->level === 'Beginner' ? 'bg-green-100 text-green-700' : ($course->level === 'Intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ $course->level }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $course->description }}</p>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                    <span>👨‍🏫 {{ $course->teacher->name }}</span>
                    <span>{{ $course->lessons_count }} lessons</span>
                    <span>👥 {{ $course->enrollments_count }}</span>
                </div>
                @if(in_array($course->id, $enrolledIds))
                    <a href="{{ route('student.course.show', $course) }}" class="block text-center btn-primary w-full">Continue Learning →</a>
                @else
                    <form action="{{ route('student.enroll', $course) }}" method="POST">
                        @csrf
                        <button class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">Enroll Free</button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-gray-400">
            <div class="text-5xl mb-3">🔍</div>
            <p class="text-lg font-medium">No courses found</p>
            <p class="text-sm">Try adjusting your search filters</p>
        </div>
        @endforelse
    </div>

    {{ $courses->links() }}
</div>
@endsection
