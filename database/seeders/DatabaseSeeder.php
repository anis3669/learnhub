<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\DiscussionPost;
use App\Models\DiscussionReply;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['admin', 'teacher', 'student'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $admin = User::firstOrCreate(['email' => 'admin@learnhub.com'], [
            'name' => 'Admin User', 'password' => Hash::make('password'), 'points' => 0, 'bio' => 'Platform Administrator',
        ]);
        $admin->syncRoles(['admin']);

        $teacher1 = User::firstOrCreate(['email' => 'teacher@learnhub.com'], [
            'name' => 'Dr. Sarah Johnson', 'password' => Hash::make('password'), 'points' => 500,
            'bio' => 'Senior Computer Science Professor with 10 years of experience.',
        ]);
        $teacher1->syncRoles(['teacher']);

        $teacher2 = User::firstOrCreate(['email' => 'teacher2@learnhub.com'], [
            'name' => 'Prof. Michael Chen', 'password' => Hash::make('password'), 'points' => 350,
            'bio' => 'Web Development expert and full-stack developer.',
        ]);
        $teacher2->syncRoles(['teacher']);

        $studentData = [
            ['name' => 'Alice Martinez', 'email' => 'alice@learnhub.com', 'points' => 850],
            ['name' => 'Bob Williams', 'email' => 'bob@learnhub.com', 'points' => 720],
            ['name' => 'Carol Davis', 'email' => 'carol@learnhub.com', 'points' => 650],
            ['name' => 'David Lee', 'email' => 'david@learnhub.com', 'points' => 580],
            ['name' => 'Emma Wilson', 'email' => 'emma@learnhub.com', 'points' => 920],
            ['name' => 'Frank Brown', 'email' => 'frank@learnhub.com', 'points' => 430],
            ['name' => 'Grace Kim', 'email' => 'grace@learnhub.com', 'points' => 770],
            ['name' => 'Henry Taylor', 'email' => 'henry@learnhub.com', 'points' => 310],
            ['name' => 'Student User', 'email' => 'student@learnhub.com', 'points' => 450],
        ];
        $students = [];
        foreach ($studentData as $sd) {
            $s = User::firstOrCreate(['email' => $sd['email']], [
                'name' => $sd['name'], 'password' => Hash::make('password'), 'points' => $sd['points'], 'bio' => 'Passionate learner.',
            ]);
            $s->syncRoles(['student']);
            $students[] = $s;
        }

        $badgesData = [
            ['name' => 'First Steps', 'description' => 'Completed your first lesson', 'icon' => '🌟', 'color' => 'yellow', 'criteria_type' => 'lessons_completed', 'criteria_value' => 1],
            ['name' => 'Quiz Master', 'description' => 'Scored 100% on a quiz', 'icon' => '🏆', 'color' => 'yellow', 'criteria_type' => 'quiz_perfect', 'criteria_value' => 1],
            ['name' => 'Fast Learner', 'description' => 'Completed 5 lessons', 'icon' => '⚡', 'color' => 'blue', 'criteria_type' => 'lessons_completed', 'criteria_value' => 5],
            ['name' => 'Scholar', 'description' => 'Completed your first course', 'icon' => '🎓', 'color' => 'green', 'criteria_type' => 'courses_completed', 'criteria_value' => 1],
            ['name' => 'Top Scorer', 'description' => 'Reached 500 points', 'icon' => '🥇', 'color' => 'orange', 'criteria_type' => 'points', 'criteria_value' => 500],
            ['name' => 'Discussion Leader', 'description' => 'Posted 5 discussion topics', 'icon' => '💬', 'color' => 'purple', 'criteria_type' => 'discussions', 'criteria_value' => 5],
            ['name' => 'Streak Master', 'description' => '7-day learning streak', 'icon' => '🔥', 'color' => 'red', 'criteria_type' => 'streak', 'criteria_value' => 7],
            ['name' => 'Explorer', 'description' => 'Enrolled in 3 courses', 'icon' => '🚀', 'color' => 'indigo', 'criteria_type' => 'enrollments', 'criteria_value' => 3],
        ];
        foreach ($badgesData as $b) {
            Badge::firstOrCreate(['name' => $b['name']], $b);
        }

        $course1 = Course::firstOrCreate(['title' => 'Introduction to Python Programming'], [
            'teacher_id' => $teacher1->id,
            'description' => 'Learn Python from scratch. This comprehensive course covers variables, loops, functions, OOP, and real-world projects. Perfect for beginners wanting to start their coding journey.',
            'category' => 'Programming', 'level' => 'Beginner', 'is_published' => true, 'duration_hours' => 12,
        ]);
        $course2 = Course::firstOrCreate(['title' => 'Web Development with Laravel'], [
            'teacher_id' => $teacher2->id,
            'description' => 'Master Laravel 12, the PHP framework for artisans. Build real-world web applications with authentication, databases, APIs and more.',
            'category' => 'Web Development', 'level' => 'Intermediate', 'is_published' => true, 'duration_hours' => 20,
        ]);
        $course3 = Course::firstOrCreate(['title' => 'Data Structures & Algorithms'], [
            'teacher_id' => $teacher1->id,
            'description' => 'Master the fundamental concepts of Data Structures and Algorithms. Learn sorting, searching, trees, graphs, and dynamic programming.',
            'category' => 'Computer Science', 'level' => 'Intermediate', 'is_published' => true, 'duration_hours' => 16,
        ]);
        $course4 = Course::firstOrCreate(['title' => 'Machine Learning Fundamentals'], [
            'teacher_id' => $teacher2->id,
            'description' => 'An introduction to Machine Learning concepts including supervised learning, neural networks, and model evaluation.',
            'category' => 'AI & ML', 'level' => 'Advanced', 'is_published' => true, 'duration_hours' => 18,
        ]);

        $lessonsByCourse = [
            $course1->id => [
                ['title' => 'Getting Started with Python', 'video_url' => 'https://www.youtube.com/watch?v=kqtD5dpn9C8', 'duration_minutes' => 15, 'order' => 1, 'content' => 'Installation, environment setup, and Hello World.'],
                ['title' => 'Variables & Data Types', 'video_url' => 'https://www.youtube.com/watch?v=cQT33yu9pY8', 'duration_minutes' => 20, 'order' => 2, 'content' => 'Integers, floats, strings, booleans and type casting.'],
                ['title' => 'Control Flow: If/Else & Loops', 'video_url' => 'https://www.youtube.com/watch?v=PqFKRqpHrjw', 'duration_minutes' => 25, 'order' => 3, 'content' => 'Conditionals and iteration with for and while loops.'],
                ['title' => 'Functions & Modules', 'video_url' => 'https://www.youtube.com/watch?v=9Os0o3wzS_I', 'duration_minutes' => 30, 'order' => 4, 'content' => 'Defining functions, parameters, return values, and imports.'],
                ['title' => 'Object-Oriented Programming', 'video_url' => 'https://www.youtube.com/watch?v=JeznW_7DlB0', 'duration_minutes' => 40, 'order' => 5, 'content' => 'Classes, objects, inheritance, and polymorphism.'],
            ],
            $course2->id => [
                ['title' => 'Laravel Installation & Setup', 'video_url' => 'https://www.youtube.com/watch?v=MFh0Fd7BsjE', 'duration_minutes' => 20, 'order' => 1, 'content' => 'Installing Laravel 12 and understanding project structure.'],
                ['title' => 'Routing & Controllers', 'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE', 'duration_minutes' => 25, 'order' => 2, 'content' => 'Creating routes, controllers, and MVC pattern.'],
                ['title' => 'Blade Templates & Views', 'video_url' => 'https://www.youtube.com/watch?v=6ePuFNnCmgI', 'duration_minutes' => 20, 'order' => 3, 'content' => 'Blade templating engine, layouts, and components.'],
                ['title' => 'Eloquent ORM & Migrations', 'video_url' => 'https://www.youtube.com/watch?v=e3Zl1lYNR7g', 'duration_minutes' => 35, 'order' => 4, 'content' => 'Database migrations, Eloquent models, and relationships.'],
            ],
            $course3->id => [
                ['title' => 'Introduction to Algorithms', 'video_url' => 'https://www.youtube.com/watch?v=0IAPZzGSbME', 'duration_minutes' => 25, 'order' => 1, 'content' => 'Big O notation, time & space complexity.'],
                ['title' => 'Arrays & Linked Lists', 'video_url' => 'https://www.youtube.com/watch?v=zg9ih6SVACc', 'duration_minutes' => 30, 'order' => 2, 'content' => 'Array operations, singly and doubly linked lists.'],
                ['title' => 'Sorting Algorithms', 'video_url' => 'https://www.youtube.com/watch?v=kPRA0W1kECg', 'duration_minutes' => 40, 'order' => 3, 'content' => 'Bubble sort, merge sort, quicksort, and time complexities.'],
                ['title' => 'Trees & Binary Search Trees', 'video_url' => 'https://www.youtube.com/watch?v=oSWTXtMglKE', 'duration_minutes' => 45, 'order' => 4, 'content' => 'Tree traversal, BST operations, and AVL trees.'],
            ],
            $course4->id => [
                ['title' => 'What is Machine Learning?', 'video_url' => 'https://www.youtube.com/watch?v=ukzFI9rgwfU', 'duration_minutes' => 20, 'order' => 1, 'content' => 'ML overview, supervised vs unsupervised learning.'],
                ['title' => 'Linear Regression', 'video_url' => 'https://www.youtube.com/watch?v=zPG4NjIkCjc', 'duration_minutes' => 30, 'order' => 2, 'content' => 'Understanding linear regression and cost functions.'],
                ['title' => 'Neural Networks Basics', 'video_url' => 'https://www.youtube.com/watch?v=aircAruvnKk', 'duration_minutes' => 45, 'order' => 3, 'content' => 'Neurons, layers, activation functions, and backpropagation.'],
            ],
        ];

        foreach ($lessonsByCourse as $courseId => $lessons) {
            foreach ($lessons as $l) {
                Lesson::firstOrCreate(
                    ['course_id' => $courseId, 'title' => $l['title']],
                    array_merge($l, ['course_id' => $courseId, 'is_published' => true])
                );
            }
        }

        $l1 = Lesson::where('course_id', $course1->id)->first();
        if ($l1 && !Quiz::where('course_id', $course1->id)->exists()) {
            $quiz1 = Quiz::create([
                'course_id' => $course1->id, 'lesson_id' => $l1->id,
                'title' => 'Python Basics Quiz', 'description' => 'Test your understanding of Python fundamentals.',
                'time_limit_minutes' => 15, 'passing_score' => 60, 'is_published' => true,
            ]);
            $quizQuestions = [
                ['q' => 'What is the correct way to print "Hello World" in Python?', 'correct' => 'print("Hello World")', 'wrong' => ['echo "Hello World"', 'console.log("Hello World")', 'printf("Hello World")']],
                ['q' => 'Which data type stores True or False in Python?', 'correct' => 'Boolean', 'wrong' => ['Integer', 'String', 'Float']],
                ['q' => 'What keyword defines a function in Python?', 'correct' => 'def', 'wrong' => ['function', 'func', 'define']],
                ['q' => 'What is the result of 5 // 2 in Python?', 'correct' => '2', 'wrong' => ['2.5', '3', '1']],
                ['q' => 'Which is a mutable data type in Python?', 'correct' => 'List', 'wrong' => ['Tuple', 'String', 'Integer']],
            ];
            foreach ($quizQuestions as $i => $qd) {
                $q = Question::create(['quiz_id' => $quiz1->id, 'question_text' => $qd['q'], 'type' => 'multiple_choice', 'points' => 10, 'order' => $i + 1]);
                $allOptions = array_merge([$qd['correct']], $qd['wrong']);
                shuffle($allOptions);
                foreach ($allOptions as $j => $opt) {
                    QuestionOption::create(['question_id' => $q->id, 'option_text' => $opt, 'is_correct' => ($opt === $qd['correct']), 'order' => $j + 1]);
                }
            }
        }

        $l3 = Lesson::where('course_id', $course3->id)->where('order', 3)->first();
        if ($l3 && !Quiz::where('course_id', $course3->id)->exists()) {
            $quiz2 = Quiz::create([
                'course_id' => $course3->id, 'lesson_id' => $l3->id,
                'title' => 'Sorting Algorithms Quiz', 'description' => 'Test your knowledge of sorting algorithms.',
                'time_limit_minutes' => 20, 'passing_score' => 60, 'is_published' => true,
            ]);
            $sortingQs = [
                ['q' => 'What is the average time complexity of QuickSort?', 'correct' => 'O(n log n)', 'wrong' => ['O(n²)', 'O(n)', 'O(log n)']],
                ['q' => 'Which sorting algorithm is best for nearly sorted arrays?', 'correct' => 'Insertion Sort', 'wrong' => ['Bubble Sort', 'Quick Sort', 'Selection Sort']],
                ['q' => 'What is the worst-case time complexity of Merge Sort?', 'correct' => 'O(n log n)', 'wrong' => ['O(n²)', 'O(n)', 'O(2^n)']],
                ['q' => 'Which algorithm uses divide and conquer?', 'correct' => 'Merge Sort', 'wrong' => ['Bubble Sort', 'Insertion Sort', 'Selection Sort']],
            ];
            foreach ($sortingQs as $i => $qd) {
                $q = Question::create(['quiz_id' => $quiz2->id, 'question_text' => $qd['q'], 'type' => 'multiple_choice', 'points' => 10, 'order' => $i + 1]);
                $allOptions = array_merge([$qd['correct']], $qd['wrong']);
                shuffle($allOptions);
                foreach ($allOptions as $j => $opt) {
                    QuestionOption::create(['question_id' => $q->id, 'option_text' => $opt, 'is_correct' => ($opt === $qd['correct']), 'order' => $j + 1]);
                }
            }
        }

        $courses = [$course1, $course2, $course3, $course4];
        foreach ($students as $i => $student) {
            $numCourses = min(3, $i + 1);
            foreach (array_slice($courses, 0, $numCourses) as $course) {
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_id' => $course->id],
                    ['progress_percent' => rand(10, 90)]
                );
            }
        }

        $discussionTopics = [
            ['title' => 'How to handle exceptions in Python?', 'body' => 'I am struggling with try/except blocks. Can someone explain the best practices for error handling in Python?'],
            ['title' => 'Best resources for learning Django after this course?', 'body' => 'I finished the Python basics section and want to build web apps. Any recommendations for Django or Flask?'],
            ['title' => 'Difference between list and tuple?', 'body' => 'I understand lists are mutable and tuples are immutable, but when should I use each one in real code?'],
        ];
        foreach ($discussionTopics as $i => $dd) {
            if (!DiscussionPost::where('title', $dd['title'])->exists()) {
                $post = DiscussionPost::create(array_merge($dd, ['course_id' => $course1->id, 'user_id' => $students[$i]->id]));
                DiscussionReply::create(['post_id' => $post->id, 'user_id' => $teacher1->id, 'body' => 'Great question! Let me explain this with some examples. This is a common topic that many beginners struggle with, but once you understand it you will find it very useful.']);
            }
        }

        $firstBadge = Badge::where('name', 'First Steps')->first();
        $topBadge = Badge::where('name', 'Top Scorer')->first();
        foreach ($students as $s) {
            if ($firstBadge) DB::table('user_badges')->updateOrInsert(['user_id' => $s->id, 'badge_id' => $firstBadge->id], ['earned_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            if ($topBadge && $s->points >= 500) DB::table('user_badges')->updateOrInsert(['user_id' => $s->id, 'badge_id' => $topBadge->id], ['earned_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        }

        $this->command->info('✅ Seeded! Login: admin@learnhub.com | teacher@learnhub.com | student@learnhub.com (password: password)');
    }
}
