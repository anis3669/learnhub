@extends('layouts.learnhub')
@section('title', 'Leaderboard')
@section('portal-name', 'Student Portal')
@section('page-title', '🏆 Leaderboard')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('student.courses') }}" class="sidebar-link">Browse Courses</a>
<a href="{{ route('student.leaderboard') }}" class="sidebar-link active">Leaderboard</a>
<a href="{{ route('student.badges') }}" class="sidebar-link">My Badges</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl p-8 text-white text-center">
        <div class="text-5xl mb-3">🏆</div>
        <h2 class="text-3xl font-bold">Global Leaderboard</h2>
        <p class="text-yellow-100 mt-1">Ranked by Merge Sort Algorithm — Compete & Climb!</p>
        @php $myPos = collect($leaderboard)->search(fn($u) => $u['id'] === Auth::id()); @endphp
        <div class="mt-4 bg-white/20 rounded-xl px-6 py-3 inline-block">
            <span class="text-2xl font-bold">#{{ $userRank }}</span>
            <span class="text-yellow-100 ml-2">Your Rank • {{ number_format($currentUser->points) }} pts</span>
        </div>
    </div>

    <!-- Algorithm note -->
    <div class="card p-4 bg-indigo-50 border-indigo-200">
        <div class="flex items-center gap-3">
            <div class="text-2xl">⚡</div>
            <div>
                <p class="text-sm font-medium text-indigo-900">Algorithm: Merge Sort</p>
                <p class="text-xs text-indigo-700">Rankings are computed using merge sort (O(n log n)) — divides the list in half recursively, sorts each half, then merges them in descending order of points.</p>
            </div>
        </div>
    </div>

    <!-- Top 3 podium -->
    @if(count($leaderboard) >= 3)
    <div class="grid grid-cols-3 gap-4">
        @foreach([1, 0, 2] as $podiumIdx)
        @if(isset($leaderboard[$podiumIdx]))
        @php $u = $leaderboard[$podiumIdx]; $rank = $podiumIdx + 1; @endphp
        <div class="card p-6 text-center {{ $podiumIdx === 0 ? 'border-yellow-300 ring-2 ring-yellow-400' : '' }} {{ $u['id'] === Auth::id() ? 'border-indigo-300 ring-2 ring-indigo-400' : '' }}">
            <div class="text-4xl mb-2">{{ $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : '🥉') }}</div>
            <div class="text-3xl font-bold text-gray-400 mb-1">#{{ $rank }}</div>
            <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-2 text-xl font-bold text-indigo-600">
                {{ strtoupper(substr($u['name'], 0, 1)) }}
            </div>
            <div class="font-semibold text-gray-900 {{ $u['id'] === Auth::id() ? 'text-indigo-600' : '' }}">
                {{ $u['name'] }} {{ $u['id'] === Auth::id() ? '(You)' : '' }}
            </div>
            <div class="text-indigo-600 font-bold text-lg mt-1">{{ number_format($u['points']) }} pts</div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    <!-- Full leaderboard -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Full Rankings</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($leaderboard as $i => $u)
            <div class="flex items-center px-6 py-4 {{ $u['id'] === Auth::id() ? 'bg-indigo-50' : 'hover:bg-gray-50' }} transition">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm mr-4 flex-shrink-0
                    {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-200 text-gray-700' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                    {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold mr-4 flex-shrink-0">
                    {{ strtoupper(substr($u['name'], 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900 {{ $u['id'] === Auth::id() ? 'text-indigo-700 font-bold' : '' }}">
                        {{ $u['name'] }} @if($u['id'] === Auth::id()) <span class="badge-pill bg-indigo-100 text-indigo-700 ml-1">You</span> @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($i < 3)
                    <span class="badge-pill {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700') }}">Top {{ $i+1 }}</span>
                    @endif
                    <span class="text-lg font-bold text-indigo-600">{{ number_format($u['points']) }}</span>
                    <span class="text-xs text-gray-400">pts</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="text-center">
        <p class="text-sm text-gray-500">Earn points by completing lessons (+20 pts), passing quizzes (+50 pts), and posting discussions (+5 pts)</p>
    </div>
</div>
@endsection
