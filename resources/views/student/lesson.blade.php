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
</a>
@endforeach
@endsection

@section('content')
<div class="space-y-6">
    <!-- Video Player -->
    <div class="card overflow-hidden">
        <div id="player-mount" class="relative w-full bg-black" style="padding-top: 56.25%;">
            @if($lesson->video_url)
            <div id="youtube-player" class="absolute inset-0 w-full h-full"></div>
            @else
            <div class="absolute inset-0 flex items-center justify-center text-white">
                <div class="text-center">
                    <div class="text-5xl mb-3">📹</div>
                    <p class="text-gray-300">No video for this lesson yet</p>
                </div>
            </div>
            @endif
        </div>

        <div class="p-6">
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $lesson->title }}</h2>
                    @if($lesson->description)
                    <p class="text-gray-600 mt-1">{{ $lesson->description }}</p>
                    @endif
                    <div class="text-xs text-gray-400 mt-1">⏱ {{ $lesson->duration_minutes }} minutes</div>
                </div>

                @if($lesson->video_url)
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Watch Progress</span>
                        <span id="watch-percentage" class="font-semibold text-gray-900">{{ $videoProgress->watch_percentage ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="watch-bar" class="bg-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $videoProgress->watch_percentage ?? 0 }}%"></div>
                    </div>
                    <p id="watch-message" class="text-xs text-gray-500">Remaining {{ 100 - ($videoProgress->watch_percentage ?? 0) }}% to unlock completion</p>
                </div>
                @endif

                <div>
                    @php
                        $isCompleted = $userProgress->is_completed;
                        $isUnlocked = ($videoProgress->watch_percentage ?? 0) >= 80;
                    @endphp

                    @if($isCompleted)
                    <div class="bg-green-100 text-green-700 px-6 py-2.5 rounded-xl font-medium flex items-center gap-2">
                        ✅ Lesson Completed!
                    </div>
                    @elseif($isUnlocked)
                    <form action="{{ route('student.lesson.complete', [$course, $lesson]) }}" method="POST">
                        @csrf
                        <button id="complete-btn" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2">
                            ✅ Mark as Complete (+20 pts)
                        </button>
                    </form>
                    <div id="success-message" class="mt-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium">
                        🎉 Congratulations! You have watched enough of this lesson.
                    </div>
                    @else
                    <button id="complete-btn" disabled class="bg-gray-300 text-gray-500 px-6 py-2.5 rounded-xl font-medium cursor-not-allowed flex items-center gap-2">
                        🔒 Mark as Complete (Watch 80%)
                    </button>
                    @endif
                </div>
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

@push('scripts')
@if($lesson->video_url)
<script>
(function() {
    const videoUrl = @json($lesson->video_url);
    const lessonId = @json($lesson->id);
    const courseId = @json($course->id);
    const videoProgress = @json($videoProgress);

    let player;
    let playerReady = false;
    let saveTimer = null;
    let currentWatchPercentage = videoProgress ? videoProgress.watch_percentage : 0;
    let currentWatchedSeconds = videoProgress ? videoProgress.watched_seconds : 0;

    const SAVE_INTERVAL = 5000;
    const UNLOCK_PERCENTAGE = 80;

    function extractYouTubeId(url) {
        url = url || '';
        if (url.includes('youtube.com/watch?v=')) {
            const id = url.split('v=')[1];
            return id ? id.split('&')[0] : null;
        }
        if (url.includes('youtu.be/')) {
            return url.split('youtu.be/')[1];
        }
        return null;
    }

    const ytId = extractYouTubeId(videoUrl);

    if (ytId) {
        const tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        window.onYouTubeIframeAPIReady = function() {
            player = new YT.Player('youtube-player', {
                height: '100%',
                width: '100%',
                videoId: ytId,
                playerVars: {
                    'playsinline': 1,
                    'rel': 0,
                    'modestbranding': 1
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        };
    }

    function onPlayerReady(event) {
        playerReady = true;
        if (currentWatchedSeconds > 0) {
            event.target.seekTo(currentWatchedSeconds, true);
        }
        startTracking();
    }

    function onPlayerStateChange(event) {
        if (event.data === YT.PlayerState.PLAYING) {
            startTracking();
        } else {
            stopTracking();
        }
    }

    function startTracking() {
        stopTracking();
        saveTimer = setInterval(trackAndSave, SAVE_INTERVAL);
    }

    function stopTracking() {
        if (saveTimer) {
            clearInterval(saveTimer);
            saveTimer = null;
        }
    }

    function trackAndSave() {
        if (!playerReady || !player || typeof player.getCurrentTime !== 'function') return;

        const currentTime = Math.floor(player.getCurrentTime());
        const duration = Math.floor(player.getDuration());

        if (duration <= 0) return;

        const watched = Math.max(currentWatchedSeconds, currentTime);
        const percentage = Math.min(100, Math.floor((watched / duration) * 100));

        if (watched === currentWatchedSeconds && percentage === currentWatchPercentage) return;

        currentWatchedSeconds = watched;
        currentWatchPercentage = percentage;

        updateUI(percentage, watched, duration);

        saveProgressToServer(duration, watched, percentage);
    }

    async function saveProgressToServer(duration, watched, percentage) {
        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('watched_seconds', watched);
            formData.append('duration_seconds', duration);
            formData.append('watch_percentage', percentage);

            await axios.post(`/student/courses/${courseId}/lessons/${lessonId}/progress`, formData);
        } catch (error) {
            console.error('Failed to save progress:', error);
        }
    }

    function updateUI(percentage, watched, duration) {
        const percentageEl = document.getElementById('watch-percentage');
        const barEl = document.getElementById('watch-bar');
        const messageEl = document.getElementById('watch-message');
        const btnEl = document.getElementById('complete-btn');
        const successEl = document.getElementById('success-message');

        if (percentageEl) percentageEl.textContent = percentage + '%';
        if (barEl) barEl.style.width = percentage + '%';

        if (messageEl) {
            const remaining = 100 - percentage;
            messageEl.textContent = remaining > 0 ? `Remaining ${remaining}% to unlock completion` : 'You have unlocked completion!';
        }

        if (percentage >= UNLOCK_PERCENTAGE) {
            if (successEl) successEl.classList.remove('hidden');
            if (btnEl && btnEl.disabled) {
                btnEl.disabled = false;
                btnEl.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                btnEl.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700', 'transition');
                btnEl.classList.remove('flex');
                // Re-insert form wrapper via simple re-render not needed, button is already in form
            }
        }
    }
})();
</script>
@endif
@endpush
