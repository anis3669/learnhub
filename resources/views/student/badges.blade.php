@extends('layouts.learnhub')
@section('title', 'My Badges')
@section('portal-name', 'Student Portal')
@section('page-title', '🎖️ My Badges')

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('student.courses') }}" class="sidebar-link">Browse Courses</a>
<a href="{{ route('student.leaderboard') }}" class="sidebar-link">Leaderboard</a>
<a href="{{ route('student.badges') }}" class="sidebar-link active">My Badges</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Your Achievement Collection</h2>
                <p class="text-yellow-100 mt-1">Keep learning to unlock more badges!</p>
                <div class="flex gap-6 mt-4">
                    <div><div class="text-3xl font-bold">{{ $earnedBadges->count() }}</div><div class="text-xs text-yellow-100">Earned</div></div>
                    <div><div class="text-3xl font-bold">{{ $allBadges->count() - $earnedBadges->count() }}</div><div class="text-xs text-yellow-100">Locked</div></div>
                    <div><div class="text-3xl font-bold">{{ number_format(Auth::user()->points) }}</div><div class="text-xs text-yellow-100">Total Points</div></div>
                </div>
            </div>
            <div class="text-8xl">🏅</div>
        </div>
    </div>

    <!-- Earned badges -->
    @if($earnedBadges->count())
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-5">✅ Earned Badges ({{ $earnedBadges->count() }})</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($earnedBadges as $badge)
            <div class="text-center p-5 bg-gradient-to-b from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl">
                <div class="text-5xl mb-3">{{ $badge->icon }}</div>
                <div class="font-semibold text-gray-900">{{ $badge->name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $badge->description }}</div>
                @if($badge->pivot->earned_at)
                <div class="text-xs text-yellow-600 font-medium mt-2">
                    Earned {{ \Carbon\Carbon::parse($badge->pivot->earned_at)->format('M d, Y') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- All badges -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-5">🔒 All Badges</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($allBadges as $badge)
            @php $earned = $earnedIds->contains($badge->id); @endphp
            <div class="text-center p-5 rounded-2xl border-2 {{ $earned ? 'border-yellow-200 bg-yellow-50' : 'border-gray-100 bg-gray-50 opacity-60' }}">
                <div class="text-5xl mb-3 {{ $earned ? '' : 'grayscale' }}">{{ $badge->icon }}</div>
                <div class="font-semibold {{ $earned ? 'text-gray-900' : 'text-gray-400' }}">{{ $badge->name }}</div>
                <div class="text-xs {{ $earned ? 'text-gray-500' : 'text-gray-400' }} mt-1">{{ $badge->description }}</div>
                @if(!$earned)
                <div class="text-xs text-gray-400 mt-2">🔒 Locked</div>
                @endif
                @if($earned)
                <div class="badge-pill bg-green-100 text-green-700 mt-2 mx-auto w-fit">✅ Earned</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- How to earn points -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">💡 How to Earn Points & Badges</h3>
        <div class="grid md:grid-cols-3 gap-4">
            @foreach([['Complete a Lesson', '+20 pts', '📖'], ['Pass a Quiz', '+50 pts', '✅'], ['Post in Discussion', '+5 pts', '💬'], ['Reply to Discussion', '+2 pts', '↩️'], ['Complete a Course', 'Scholar Badge 🎓', '🏆'], ['Score 100% on Quiz', 'Quiz Master Badge 🏆', '🧠']] as $r)
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3">
                <span class="text-2xl">{{ $r[2] }}</span>
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $r[0] }}</div>
                    <div class="text-sm text-indigo-600 font-bold">{{ $r[1] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
