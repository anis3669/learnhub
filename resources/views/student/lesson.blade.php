@extends('layouts.learnhub')
@section('title', $lesson->title)
@section('portal-name', 'Student Portal')
@section('page-title', $lesson->title)
@section('breadcrumb', $course->title . ' → ' . $lesson->title)

@section('sidebar-nav')
<a href="{{ route('student.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('student.course.show', $course) }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Back to Course
</a>
<div class="pt-2 pb-1 px-1 text-xs text-gray-400 uppercase tracking-wider">Course Lessons</div>
@foreach($course->lessons as $l)
<a href="{{ route('student.lesson', [$course, $l]) }}" class="sidebar-link text-xs {{ $l->id === $lesson->id ? 'active' : '' }}">
    @php $p = \App\Models\UserProgress::where('user_id', Auth::id())->where('lesson_id', $l->id)->first(); @endphp
    {{ $p?->is_completed ? '✅' : ($l->id === $lesson->id ? '▶️' : '○') }}
    <span class="truncate ml-1">{{ $l->title }}</span>
    @if($p && !$p->is_completed && $p->watch_percent > 0)
    <span class="ml-auto text-gray-400">{{ $p->watch_percent }}%</span>
    @endif
</a>
@endforeach
@endsection

@section('content')
@php
    $isYouTube = $lesson->video_url && (str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'));
    $isVimeo   = $lesson->video_url && str_contains($lesson->video_url, 'vimeo.com');
    $embedUrl  = $lesson->embed_url;
    if ($isYouTube) {
        $embedUrl = rtrim($embedUrl, '/') . '?enablejsapi=1&rel=0&modestbranding=1';
    }
    if ($isVimeo) {
        $embedUrl = rtrim($embedUrl, '/') . '?api=1';
    }
@endphp

<div class="space-y-6"
     x-data="{
        watchPercent: {{ $userProgress->watch_percent ?? 0 }},
        canComplete: {{ ($userProgress->watch_percent ?? 0) >= 95 ? 'true' : 'false' }},
        isCompleted: {{ $userProgress->is_completed ? 'true' : 'false' }},
        saving: false,
        updateProgress(percent) {
            if (this.saving || percent <= this.watchPercent) return;
            this.saving = true;
            fetch('{{ route('student.lesson.progress', $lesson) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ watch_percent: percent })
            })
            .then(r => r.json())
            .then(data => {
                this.watchPercent = data.watch_percent;
                this.canComplete  = data.can_complete;
                this.saving = false;
            })
            .catch(() => { this.saving = false; });
        }
     }"
     @video-progress.document="updateProgress($event.detail.percent)">

    <!-- Video Player -->
    <div class="card overflow-hidden">
        @if($lesson->video_url)
        <div class="relative w-full" style="padding-top: 56.25%;">
            @if($isYouTube)
            <iframe id="yt-player"
                src="{{ $embedUrl }}"
                class="absolute inset-0 w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
            @elseif($isVimeo)
            <iframe id="vimeo-player"
                src="{{ $embedUrl }}"
                class="absolute inset-0 w-full h-full"
                frameborder="0"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen>
            </iframe>
            @else
            <iframe
                src="{{ $embedUrl }}"
                class="absolute inset-0 w-full h-full"
                frameborder="0"
                allowfullscreen>
            </iframe>
            @endif
        </div>

        <!-- Progress bar under video -->
        @if(!$userProgress->is_completed)
        <div class="px-6 pt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                <span class="font-medium">Watch Progress</span>
                <span class="font-bold" :class="canComplete ? 'text-green-600' : 'text-indigo-600'" x-text="watchPercent + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                <div class="h-2 rounded-full transition-all"
                     :class="canComplete ? 'bg-green-500' : 'bg-indigo-500'"
                     :style="'width: ' + watchPercent + '%'"></div>
            </div>
            <p class="text-xs text-gray-400" x-show="!canComplete">Watch at least 95% to unlock lesson completion</p>
            <p class="text-xs text-green-600 font-medium" x-show="canComplete">✅ You can now mark this lesson as complete!</p>
        </div>
        @endif
        @else
        <div class="h-64 bg-gray-900 flex items-center justify-center text-white">
            <div class="text-center">
                <div class="text-5xl mb-3">📹</div>
                <p class="text-gray-300">No video for this lesson yet</p>
            </div>
        </div>
        @endif

        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $lesson->title }}</h2>
                    @if($lesson->description)
                    <p class="text-gray-600 mt-1">{{ $lesson->description }}</p>
                    @endif
                    <div class="text-xs text-gray-400 mt-1">⏱ {{ $lesson->duration_minutes }} minutes</div>
                </div>

                @if($userProgress->is_completed)
                <div class="bg-green-100 text-green-700 px-6 py-2.5 rounded-xl font-medium flex items-center gap-2">
                    ✅ Lesson Completed!
                </div>
                @elseif($lesson->video_url)
                <div>
                    <form action="{{ route('student.lesson.complete', [$course, $lesson]) }}" method="POST" x-show="canComplete">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2 shadow-sm">
                            ✅ Mark as Complete (+20 pts)
                        </button>
                    </form>
                    <div x-show="!canComplete" class="bg-gray-100 text-gray-500 px-6 py-2.5 rounded-xl font-medium flex items-center gap-2 cursor-not-allowed">
                        🔒 Watch 95% to Unlock
                    </div>
                </div>
                @else
                <form action="{{ route('student.lesson.complete', [$course, $lesson]) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2">
                        ✅ Mark as Complete (+20 pts)
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Content -->
    @if($lesson->content)
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-3">📝 Lesson Notes</h3>
        <div class="prose text-gray-700">{{ $lesson->content }}</div>
    </div>
    @endif

    <!-- Quiz CTA -->
    @if($quiz)
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg">🧠 Ready to Test Your Knowledge?</h3>
                <p class="text-purple-100 mt-1">{{ $quiz->title }} • {{ $quiz->time_limit_minutes }}min • Pass: {{ $quiz->passing_score }}%</p>
            </div>
            <a href="{{ route('student.quiz', [$course, $quiz]) }}" class="bg-white text-purple-700 px-6 py-2.5 rounded-xl font-bold hover:bg-purple-50 transition">
                Take Quiz →
            </a>
        </div>
    </div>
    @endif

    <!-- Navigation -->
    <div class="flex items-center justify-between">
        @if($prev)
        <a href="{{ route('student.lesson', [$course, $prev]) }}" class="btn-secondary flex items-center gap-2">
            ← Previous: {{ Str::limit($prev->title, 30) }}
        </a>
        @else
        <div></div>
        @endif
        @if($next)
        <a href="{{ route('student.lesson', [$course, $next]) }}" class="btn-primary flex items-center gap-2">
            Next: {{ Str::limit($next->title, 30) }} →
        </a>
        @else
        <a href="{{ route('student.course.show', $course) }}" class="btn-primary">
            Back to Course →
        </a>
        @endif
    </div>
</div>

@if($isYouTube && !$userProgress->is_completed)
<script>
(function() {
    var tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    var first = document.getElementsByTagName('script')[0];
    first.parentNode.insertBefore(tag, first);

    var player;
    var lastReported = {{ $userProgress->watch_percent ?? 0 }};
    var trackInterval;

    window.onYouTubeIframeAPIReady = function() {
        player = new YT.Player('yt-player', {
            events: {
                onStateChange: function(e) {
                    if (e.data === YT.PlayerState.PLAYING) {
                        trackInterval = setInterval(function() {
                            var duration = player.getDuration();
                            var current  = player.getCurrentTime();
                            if (duration > 0) {
                                var pct = Math.floor((current / duration) * 100);
                                if (pct > lastReported && pct % 5 === 0) {
                                    lastReported = pct;
                                    dispatchProgress(pct);
                                }
                            }
                        }, 5000);
                    } else {
                        clearInterval(trackInterval);
                    }
                },
                onReady: function() {
                    var duration = player.getDuration();
                    var current  = player.getCurrentTime();
                    if (duration > 0) {
                        var pct = Math.floor((current / duration) * 100);
                        if (pct > lastReported) {
                            lastReported = pct;
                            dispatchProgress(pct);
                        }
                    }
                }
            }
        });
    };

    function dispatchProgress(percent) {
        var event = new CustomEvent('video-progress', { detail: { percent: percent } });
        document.dispatchEvent(event);
    }
})();
</script>
@endif

@if($isVimeo && !$userProgress->is_completed)
<script src="https://player.vimeo.com/api/player.js"></script>
<script>
(function() {
    var iframe = document.getElementById('vimeo-player');
    if (!iframe) return;
    var player = new Vimeo.Player(iframe);
    var lastReported = {{ $userProgress->watch_percent ?? 0 }};

    player.on('timeupdate', function(data) {
        var pct = Math.floor(data.percent * 100);
        if (pct > lastReported && pct % 5 === 0) {
            lastReported = pct;
            document.dispatchEvent(new CustomEvent('video-progress', { detail: { percent: pct } }));
        }
    });
})();
</script>
@endif
@endsection
