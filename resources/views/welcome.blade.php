<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LearnHub - Modern Learning Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Nav -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">LearnHub</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Log in</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:text-gray-900">Get Started Free</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="gradient-bg text-white py-24 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center bg-white/20 rounded-full px-4 py-2 text-sm mb-6">
                <span class="text-yellow-300 mr-2">✨</span> The Future of Online Learning
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                Learn Smarter,<br><span class="text-yellow-300">Grow Faster</span>
            </h1>
            <p class="text-xl md:text-2xl text-white/80 mb-10 max-w-3xl mx-auto">
                Video lessons, interactive quizzes, live leaderboards, and gamification. Master new skills with LearnHub.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-yellow-50 transition">
                    Start Learning Free 
                </a>
                <a href="#features" class="border-2 border-white/40 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition">
                    Explore Courses
                </a>
            </div>
            <div class="mt-16 grid grid-cols-3 gap-8 max-w-lg mx-auto text-center">
                <div><div class="text-3xl font-bold">50+</div><div class="text-white/70 text-sm">Courses</div></div>
                <div><div class="text-3xl font-bold">1000+</div><div class="text-white/70 text-sm">Students</div></div>
                <div><div class="text-3xl font-bold">98%</div><div class="text-white/70 text-sm">Satisfaction</div></div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Everything You Need to Succeed</h2>
                <p class="text-gray-600 text-lg">A complete learning ecosystem built for modern students and educators.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['icon'=>'🎬', 'title'=>'Video Streaming', 'desc'=>'HD video lessons with YouTube & Vimeo integration. Learn at your own pace.', 'color'=>'bg-blue-50 text-blue-600'],
                    ['icon'=>'🧠', 'title'=>'Smart Quizzes', 'desc'=>'Auto-graded quizzes with instant feedback. Track your understanding in real time.', 'color'=>'bg-purple-50 text-purple-600'],
                    ['icon'=>'🏆', 'title'=>'Leaderboard (Merge Sort)', 'desc'=>'Competitive leaderboard powered by merge sort algorithm. Rise to the top!', 'color'=>'bg-yellow-50 text-yellow-600'],
                    ['icon'=>'🎖️', 'title'=>'Gamification & Badges', 'desc'=>'Earn points and badges as you complete lessons, quizzes, and discussions.', 'color'=>'bg-green-50 text-green-600'],
                    ['icon'=>'💬', 'title'=>'Discussion Forums', 'desc'=>'Engage with peers and instructors in course-specific discussion boards.', 'color'=>'bg-pink-50 text-pink-600'],
                    ['icon'=>'📊', 'title'=>'Progress Analytics', 'desc'=>'Detailed dashboards for students, teachers, and admins to track performance.', 'color'=>'bg-indigo-50 text-indigo-600'],
                ] as $f)
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                    <div class="w-14 h-14 {{ explode(' ', $f['color'])[0] }} rounded-xl flex items-center justify-center text-2xl mb-4">{{ $f['icon'] }}</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                    <p class="text-gray-600">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Roles -->
    <section class="bg-gray-100 py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Built for Everyone</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['role'=>'Student', 'icon'=>'👨‍🎓', 'color'=>'from-blue-500 to-indigo-600', 'features'=>['Enroll in courses', 'Watch video lessons', 'Take auto-graded quizzes', 'Track your progress', 'Compete on leaderboards', 'Earn badges & points']],
                    ['role'=>'Teacher', 'icon'=>'👩‍🏫', 'color'=>'from-purple-500 to-pink-600', 'features'=>['Create & manage courses', 'Upload video lessons', 'Build custom quizzes', 'Monitor student progress', 'Respond to discussions', 'View submission reports']],
                    ['role'=>'Admin', 'icon'=>'⚙️', 'color'=>'from-green-500 to-teal-600', 'features'=>['Manage all users', 'Approve teachers', 'Manage courses', 'View system reports', 'Manage badges', 'Monitor activity']],
                ] as $r)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm card-hover">
                    <div class="bg-gradient-to-r {{ $r['color'] }} p-8 text-white text-center">
                        <div class="text-5xl mb-2">{{ $r['icon'] }}</div>
                        <h3 class="text-2xl font-bold">{{ $r['role'] }}</h3>
                    </div>
                    <div class="p-8">
                        <ul class="space-y-3">
                            @foreach($r['features'] as $f)
                            <li class="flex items-center text-gray-700"><svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $f }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="gradient-bg py-20 px-4 text-white text-center">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-4xl font-bold mb-4">Ready to Start Learning?</h2>
            <p class="text-white/80 text-lg mb-8">Join thousands of students already on LearnHub.</p>
            <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-10 py-4 rounded-xl font-bold text-lg hover:bg-yellow-50 transition inline-block">
                Create Free Account →
            </a>
            <div class="mt-6 text-white/60 text-sm">
                Demo accounts: student@learnhub.com | teacher@learnhub.com | admin@learnhub.com (password: <strong>password</strong>)
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8 px-4 text-center">
        <div class="flex items-center justify-center space-x-2 mb-2">
            <div class="w-6 h-6 bg-indigo-600 rounded flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
            </div>
            <span class="text-white font-semibold">LearnHub</span>
        </div>
        <p class="text-sm">© {{ date('Y') }} LearnHub. Built with Laravel 12 + Tailwind CSS.</p>
    </footer>
</body>
</html>
