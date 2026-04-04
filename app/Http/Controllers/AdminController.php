<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Badge;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $recentUsers = User::latest()->take(5)->get();
        $popularCourses = Course::withCount('enrollments')->orderByDesc('enrollments_count')->take(5)->get();
        $recentAttempts = QuizAttempt::with('user', 'quiz.course')->whereNotNull('completed_at')->latest()->take(5)->get();
        $monthlyEnrollments = $this->getMonthlyStats();
        return view('admin.dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalCourses', 'totalEnrollments',
            'recentUsers', 'popularCourses', 'recentAttempts', 'monthlyEnrollments'
        ));
    }

    public function users(Request $request)
    {
        $query = User::with('roles');
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.user-create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,teacher,student',
        ]);
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password)]);
        $user->assignRole($request->role);
        return redirect()->route('admin.users')->with('success', "User created and assigned role: {$request->role}");
    }

    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.user-edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate(['name' => 'required|max:255', 'email' => 'required|email|unique:users,email,' . $user->id, 'role' => 'required']);
        $user->update(['name' => $request->name, 'email' => $request->email]);
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        $user->syncRoles([$request->role]);
        return redirect()->route('admin.users')->with('success', 'User updated!');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) return redirect()->back()->with('error', 'Cannot delete yourself.');
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }

    public function courses(Request $request)
    {
        $query = Course::with('teacher')->withCount('enrollments', 'lessons');
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $courses = $query->latest()->paginate(20);
        return view('admin.courses', compact('courses'));
    }

    public function toggleCourse(Course $course)
    {
        $course->update(['is_published' => !$course->is_published]);
        $status = $course->is_published ? 'published' : 'unpublished';
        return redirect()->back()->with('success', "Course {$status} successfully.");
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }

    public function reports()
    {
        $usersByRole = [
            'Students' => User::role('student')->count(),
            'Teachers' => User::role('teacher')->count(),
            'Admins' => User::role('admin')->count(),
        ];
        $coursesByCategory = Course::selectRaw('category, count(*) as count')->groupBy('category')->pluck('count', 'category');
        $coursesByLevel = Course::selectRaw('level, count(*) as count')->groupBy('level')->pluck('count', 'level');
        $topStudents = User::role('student')->orderByDesc('points')->take(10)->get();
        $passRate = QuizAttempt::whereNotNull('completed_at')->selectRaw('count(*) as total, sum(case when passed then 1 else 0 end) as passed')->first();
        $avgScore = QuizAttempt::whereNotNull('completed_at')->selectRaw('avg(case when total_points > 0 then score * 100.0 / total_points else 0 end) as avg')->value('avg');
        $completionStats = [
            'total_enrollments' => Enrollment::count(),
            'completed' => Enrollment::whereNotNull('completed_at')->count(),
            'in_progress' => Enrollment::whereNull('completed_at')->where('progress_percent', '>', 0)->count(),
        ];
        return view('admin.reports', compact('usersByRole', 'coursesByCategory', 'coursesByLevel', 'topStudents', 'passRate', 'avgScore', 'completionStats'));
    }

    public function badges()
    {
        $badges = Badge::withCount('users')->get();
        return view('admin.badges', compact('badges'));
    }

    public function storeBadge(Request $request)
    {
        $request->validate(['name' => 'required|max:100', 'description' => 'required', 'icon' => 'required', 'criteria_type' => 'required', 'criteria_value' => 'required|integer']);
        Badge::create($request->only('name', 'description', 'icon', 'color', 'criteria_type', 'criteria_value'));
        return redirect()->back()->with('success', 'Badge created!');
    }

    public function deleteBadge(Badge $badge)
    {
        $badge->delete();
        return redirect()->back()->with('success', 'Badge deleted.');
    }

    private function getMonthlyStats(): array
    {
        $stats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $stats[] = [
                'month' => $date->format('M Y'),
                'enrollments' => Enrollment::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'users' => User::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        }
        return $stats;
    }
}
