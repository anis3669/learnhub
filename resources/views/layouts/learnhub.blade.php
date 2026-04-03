<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LearnHub') - LearnHub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { @apply flex items-center px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-all text-sm font-medium; }
        .sidebar-link.active { @apply bg-indigo-600 text-white hover:bg-indigo-700 hover:text-white; }
        .sidebar-link svg { @apply mr-3 flex-shrink-0; }
        .stat-card { @apply bg-white rounded-2xl p-6 shadow-sm border border-gray-100; }
        .btn-primary { @apply bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition; }
        .btn-secondary { @apply bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition; }
        .btn-danger { @apply bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-200 transition; }
        .card { @apply bg-white rounded-2xl shadow-sm border border-gray-100; }
        .badge-pill { @apply inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <!-- Logo -->
            <div class="flex items-center space-x-3 px-6 py-5 border-b border-gray-100">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-gray-900">LearnHub</div>
                    <div class="text-xs text-gray-500">@yield('portal-name', 'Portal')</div>
                </div>
            </div>

            <!-- User info -->
            <div class="px-4 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <img src="{{ Auth::user()->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">
                            @foreach(Auth::user()->roles as $role)
                                <span class="capitalize">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if(Auth::user()->hasRole('student'))
                <div class="mt-3 bg-indigo-50 rounded-lg px-3 py-2 flex items-center justify-between">
                    <span class="text-xs text-indigo-600 font-medium">⭐ Points</span>
                    <span class="text-sm font-bold text-indigo-700">{{ number_format(Auth::user()->points) }}</span>
                </div>
                @endif
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                @yield('sidebar-nav')
            </nav>

            <!-- Logout -->
            <div class="px-4 py-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('breadcrumb')
                        <div class="text-xs text-gray-500 mt-0.5">@yield('breadcrumb')</div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @yield('header-actions')
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-sm">
                            <img src="{{ Auth::user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                            <span class="hidden md:block font-medium text-gray-700">{{ Auth::user()->name }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile Settings</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash messages -->
            @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                <span>✅ {{ session('success') }}</span>
                <button @click="show = false" class="text-green-600 hover:text-green-800">✕</button>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                <span>❌ {{ session('error') }}</span>
                <button @click="show = false" class="text-red-600 hover:text-red-800">✕</button>
            </div>
            @endif

            <!-- Page content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
