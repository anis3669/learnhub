@extends('layouts.learnhub')
@section('title', 'Manage Users')
@section('portal-name', 'Admin Panel')
@section('page-title', 'Manage Users')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('admin.users') }}" class="sidebar-link active">Users</a>
<a href="{{ route('admin.courses') }}" class="sidebar-link">Courses</a>
<a href="{{ route('admin.reports') }}" class="sidebar-link">Reports</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link">Badges</a>
@endsection

@section('header-actions')
<a href="{{ route('admin.user.create') }}" class="btn-primary">+ Add User</a>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filter -->
    <form method="GET" class="card p-4">
        <div class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="role" class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm">
                <option value="">All Roles</option>
                @foreach(['student', 'teacher', 'admin'] as $r)
                <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','role']))<a href="{{ route('admin.users') }}" class="btn-secondary">Clear</a>@endif
        </div>
    </form>

    <!-- Users table -->
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">User</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Role</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Points</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Joined</th>
                    <th class="text-left px-6 py-4 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-200 flex items-center justify-center text-sm font-bold text-indigo-700">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                        <span class="badge-pill {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' : ($role->name === 'teacher' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 font-bold text-indigo-600">{{ number_format($user->points) }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.user.edit', $user) }}" class="text-indigo-600 hover:underline text-xs">Edit</a>
                            @if($user->id !== Auth::id())
                            <form action="{{ route('admin.user.delete', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
