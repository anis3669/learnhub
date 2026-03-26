<x-guest-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-2">👋 Welcome back, {{ auth()->user()->name ?? 'Student' }}!</h1>
        <p class="text-gray-600 mb-8">Here's your learning progress</p>

        <!-- Leaderboard Section -->
        <div class="mb-10">
            <h2 class="text-xl font-semibold mb-4">🏆 Overall Leaderboard</h2>
            <livewire:leaderboard />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- My Courses -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-semibold text-lg mb-4">📚 My Courses</h3>
                <p class="text-gray-500">No courses enrolled yet. (We'll add this soon)</p>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-semibold text-lg mb-4">📅 Recent Quizzes</h3>
                <p class="text-gray-500">No recent quizzes. (We'll connect real data soon)</p>
            </div>
        </div>
    </div>
</x-guest-layout>
