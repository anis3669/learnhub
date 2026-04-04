<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LearnHub - Modern Learning Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
        }
        .feature-icon-bg {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(0,0,0,0.10);
        }
        .stat-divider:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,0.15);
        }
    </style>
</head>
<body class="bg-white antialiased">

    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">LearnHub</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Features</a>
                    <a href="#how-it-works" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">How it Works</a>
                    <a href="#testimonials" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Testimonials</a>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition px-3 py-2">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-500 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-500 rounded-full opacity-10 blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 relative">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-flex items-center bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-sm font-medium text-indigo-200 mb-8">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    Trusted by 1,000+ learners worldwide
                </span>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-[1.1] tracking-tight">
                    Unlock Your<br>
                    <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
                        Full Potential
                    </span>
                </h1>
                <p class="text-lg md:text-xl text-indigo-200 mb-10 max-w-2xl mx-auto leading-relaxed">
                    A professional learning platform with video lessons, interactive quizzes, real-time leaderboards, and gamification — all in one place.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-8 py-4 rounded-xl font-bold text-base hover:bg-yellow-50 transition shadow-lg">
                        Start for Free →
                    </a>
                    <a href="{{ route('login') }}" class="border border-white/30 text-white px-8 py-4 rounded-xl font-semibold text-base hover:bg-white/10 transition">
                        Sign In
                    </a>
                </div>
                <div class="mt-20 grid grid-cols-3 gap-0 max-w-md mx-auto">
                    <div class="stat-divider px-6 py-2 text-center">
                        <div class="text-3xl font-extrabold text-white">50+</div>
                        <div class="text-indigo-300 text-xs font-medium mt-1 uppercase tracking-wide">Courses</div>
                    </div>
                    <div class="stat-divider px-6 py-2 text-center">
                        <div class="text-3xl font-extrabold text-white">1k+</div>
                        <div class="text-indigo-300 text-xs font-medium mt-1 uppercase tracking-wide">Students</div>
                    </div>
                    <div class="px-6 py-2 text-center">
                        <div class="text-3xl font-extrabold text-white">98%</div>
                        <div class="text-indigo-300 text-xs font-medium mt-1 uppercase tracking-wide">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Platform Features</span>
                <h2 class="text-4xl font-extrabold text-gray-900 mt-3 mb-4">Everything in One Platform</h2>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">Designed to give students, teachers, and institutions the tools they need to succeed.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['icon'=>'M15 10l4.553-2.169A1 1 0 0121 8.763V15.5a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z', 'title'=>'Video Lessons', 'desc'=>'Stream HD video content with YouTube & Vimeo integration. Pause, rewind, and learn at your own pace.', 'accent'=>'from-blue-500 to-blue-600'],
                    ['icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'title'=>'Smart Quizzes', 'desc'=>'Auto-graded assessments with instant feedback. Identify knowledge gaps and reinforce learning.', 'accent'=>'from-violet-500 to-purple-600'],
                    ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title'=>'Live Leaderboard', 'desc'=>'Real-time rankings powered by merge sort algorithm. Stay motivated by competing with peers.', 'accent'=>'from-amber-500 to-orange-500'],
                    ['icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'title'=>'Badges & Points', 'desc'=>'Gamified learning with XP points and achievement badges earned through milestones and participation.', 'accent'=>'from-emerald-500 to-green-600'],
                    ['icon'=>'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'title'=>'Discussion Forums', 'desc'=>'Course-specific discussion boards where students and teachers collaborate and share knowledge.', 'accent'=>'from-pink-500 to-rose-500'],
                    ['icon'=>'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'title'=>'Analytics Dashboard', 'desc'=>'Rich reporting for every role — track completion rates, quiz scores, and engagement trends.', 'accent'=>'from-sky-500 to-cyan-600'],
                ] as $f)
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $f['accent'] }} flex items-center justify-center mb-5 shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="py-24 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Simple Process</span>
                <h2 class="text-4xl font-extrabold text-gray-900 mt-3 mb-4">Get Started in Minutes</h2>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">From sign-up to your first lesson — it takes less than two minutes.</p>
            </div>
            <div class="grid md:grid-cols-4 gap-8 relative">
                @foreach([
                    ['step'=>'01', 'title'=>'Create Account', 'desc'=>'Sign up in seconds. No credit card required.'],
                    ['step'=>'02', 'title'=>'Browse Courses', 'desc'=>'Explore the catalog and enroll in courses that match your goals.'],
                    ['step'=>'03', 'title'=>'Learn & Practise', 'desc'=>'Watch lessons, complete quizzes, and join discussions.'],
                    ['step'=>'04', 'title'=>'Earn & Grow', 'desc'=>'Collect badges, climb the leaderboard, and track your progress.'],
                ] as $s)
                <div class="text-center relative">
                    <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-md">
                        <span class="text-white font-black text-lg">{{ $s['step'] }}</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">{{ $s['title'] }}</h3>
                    <p class="text-gray-500 text-sm">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-24 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Testimonials</span>
                <h2 class="text-4xl font-extrabold text-gray-900 mt-3 mb-4">Loved by Learners</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['name'=>'Emma Wilson', 'role'=>'Computer Science Student', 'avatar'=>'EW', 'color'=>'bg-indigo-100 text-indigo-700', 'quote'=>'LearnHub completely changed how I study. The quizzes and leaderboard keep me motivated every single day.'],
                    ['name'=>'Dr. Sarah Johnson', 'role'=>'Course Instructor', 'avatar'=>'SJ', 'color'=>'bg-purple-100 text-purple-700', 'quote'=>'Building courses on LearnHub is effortless. My students are more engaged than ever, and the progress analytics are invaluable.'],
                    ['name'=>'Marcus Lee', 'role'=>'Full-Stack Developer', 'avatar'=>'ML', 'color'=>'bg-emerald-100 text-emerald-700', 'quote'=>'I landed my first dev job after completing three courses here. The structured content and instant quiz feedback made all the difference.'],
                ] as $t)
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                    <div class="flex items-center mb-5">
                        <div class="w-11 h-11 rounded-full {{ $t['color'] }} flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">
                            {{ $t['avatar'] }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">{{ $t['name'] }}</div>
                            <div class="text-gray-400 text-xs">{{ $t['role'] }}</div>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">"{{ $t['quote'] }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="hero-bg py-24 px-4 text-white text-center relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-purple-500 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-500 rounded-full opacity-10 blur-3xl"></div>
        </div>
        <div class="max-w-2xl mx-auto relative">
            <h2 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight">Begin Your Journey Today</h2>
            <p class="text-indigo-200 text-lg mb-10">Join thousands of students already building skills on LearnHub.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-10 py-4 rounded-xl font-bold text-base hover:bg-yellow-50 transition shadow-lg inline-block">
                    Create Free Account →
                </a>
                <a href="{{ route('login') }}" class="border border-white/30 text-white px-10 py-4 rounded-xl font-semibold text-base hover:bg-white/10 transition inline-block">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-500 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold">LearnHub</span>
                </div>
                <div class="flex items-center space-x-8 text-sm">
                    <a href="#features" class="hover:text-gray-300 transition">Features</a>
                    <a href="#how-it-works" class="hover:text-gray-300 transition">How it Works</a>
                    <a href="{{ route('login') }}" class="hover:text-gray-300 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-300 transition">Register</a>
                </div>
                <p class="text-sm">© {{ date('Y') }} LearnHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
