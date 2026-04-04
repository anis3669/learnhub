@extends('layouts.learnhub')
@section('title', 'Edit User')
@section('portal-name', 'Admin Panel')
@section('page-title', 'Edit User')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">Dashboard</a>
<a href="{{ route('admin.users') }}" class="sidebar-link">Users</a>
<a href="{{ route('admin.courses') }}" class="sidebar-link">Courses</a>
<a href="{{ route('admin.reports') }}" class="sidebar-link">Reports</a>
<a href="{{ route('admin.badges') }}" class="sidebar-link">Badges</a>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">✏️ Edit User: {{ $user->name }}</h2>
        <form action="{{ route('admin.user.update', $user) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password (leave blank to keep)</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                <select name="role" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm">
                    @foreach(['student', 'teacher', 'admin'] as $r)
                    <option value="{{ $r }}" {{ $user->hasRole($r) ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('admin.users') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
