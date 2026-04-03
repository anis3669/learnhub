@extends('layouts.learnhub')
@section('title', 'Reports')
@section('portal-name', 'Admin Panel')
@section('page-title', '📊 Platform Reports')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('admin.users') }}" class="sidebar-link">Users</a>
<a href="{{ route('admin.courses') }}" class="sidebar-link">Courses</a>
<a href="{{ route('admin.reports') }}" class="sidebar-link active">Reports</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link">Badges</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- User breakdown -->
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">👥 Users by Role</h3>
            @foreach($usersByRole as $role => $count)
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-700">{{ $role }}</span>
                <div class="flex items-center gap-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ array_sum($usersByRole) > 0 ? ($count / array_sum($usersByRole) * 100) : 0 }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 w-8 text-right">{{ $count }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">📚 Courses by Category</h3>
            @foreach($coursesByCategory as $cat => $count)
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-700">{{ $cat }}</span>
                <span class="badge-pill bg-indigo-100 text-indigo-700">{{ $count }}</span>
            </div>
            @endforeach
        </div>

        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">⭐ Courses by Level</h3>
            @foreach($coursesByLevel as $level => $count)
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-700">{{ $level }}</span>
                <span class="badge-pill {{ $level === 'Beginner' ? 'bg-green-100 text-green-700' : ($level === 'Intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Quiz stats -->
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="stat-card text-center">
            <div class="text-4xl font-bold text-indigo-600">{{ $passRate->total ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Quiz Attempts</div>
        </div>
        <div class="stat-card text-center">
            <div class="text-4xl font-bold text-green-600">
                {{ $passRate->total > 0 ? round(($passRate->passed / $passRate->total) * 100) : 0 }}%
            </div>
            <div class="text-sm text-gray-500 mt-1">Pass Rate</div>
        </div>
        <div class="stat-card text-center">
            <div class="text-4xl font-bold text-purple-600">{{ round($avgScore ?? 0) }}%</div>
            <div class="text-sm text-gray-500 mt-1">Average Score</div>
        </div>
    </div>

    <!-- Completion stats -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">🎓 Enrollment Completion</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-blue-600">{{ $completionStats['total_enrollments'] }}</div>
                <div class="text-sm text-gray-500">Total Enrollments</div>
            </div>
            <div class="bg-green-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-green-600">{{ $completionStats['in_progress'] }}</div>
                <div class="text-sm text-gray-500">In Progress</div>
            </div>
            <div class="bg-purple-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-purple-600">{{ $completionStats['completed'] }}</div>
                <div class="text-sm text-gray-500">Completed</div>
            </div>
        </div>
    </div>

    <!-- Top Students -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">🏆 Top Students by Points</h3>
        <div class="space-y-2">
            @foreach($topStudents as $i => $student)
            <div class="flex items-center justify-between p-3 rounded-xl {{ $i < 3 ? 'bg-yellow-50' : 'bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                        {{ $i === 0 ? 'bg-yellow-200 text-yellow-800' : ($i === 1 ? 'bg-gray-200 text-gray-700' : ($i === 2 ? 'bg-orange-200 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                        {{ $i < 3 ? ['🥇','🥈','🥉'][$i] : ($i+1) }}
                    </div>
                    <div class="font-medium text-gray-800">{{ $student->name }}</div>
                </div>
                <div class="font-bold text-indigo-600">{{ number_format($student->points) }} pts</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
