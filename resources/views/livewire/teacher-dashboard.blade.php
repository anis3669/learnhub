<x-guest-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-2">👋 Welcome, Teacher {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mb-8">Manage your courses and students</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- My Courses -->
            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-semibold text-lg mb-4">📚 My Courses</h3>
                <p class="text-gray-500">You have not created any courses yet.</p>
                <a href="/admin/courses" class="mt-4 inline-block text-blue-600 hover:underline">
                    → Go to Course Management
                </a>
            </div>

            <!-- Total Students -->
            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-semibold text-lg mb-4">👨‍🎓 Total Students</h3>
                <p class="text-5xl font-bold text-green-600">0</p>
                <p class="text-sm text-gray-500">Enrolled in your courses</p>
            </div>

            <!-- Recent Quizzes -->
            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-semibold text-lg mb-4">📝 Recent Quizzes</h3>
                <p class="text-gray-500">No quizzes created yet.</p>
            </div>
        </div>
    </div>
</x-guest-layout>