# LearnHub - E-Learning Platform

## Overview
A full-featured e-learning platform built with Laravel 12 + PostgreSQL + Tailwind CSS + Alpine.js. Features video streaming, auto-graded quizzes, merge sort leaderboard, gamification, and role-based portals.

## Architecture

### Tech Stack
- **Backend**: Laravel 12 (PHP 8.3)
- **Database**: PostgreSQL (via PGHOST/PGPORT/PGDATABASE/PGUSER/PGPASSWORD secrets)
- **Frontend**: Tailwind CSS v4 + Alpine.js v3 + Vite
- **Auth/Roles**: Laravel Breeze + Spatie Laravel Permission
- **Server**: `php artisan serve` on port 5000

### Roles
- **student** — browse/enroll in courses, watch lessons, take quizzes, leaderboard, badges
- **teacher** — create/manage courses, lessons, quizzes, view student progress
- **admin** — manage all users, courses, badges, view platform reports

### Algorithm
Leaderboard uses **Merge Sort** (O(n log n)) implemented in `StudentController::mergeSort()` and `merge()`. Sorts all students by points descending.

## Project Structure

### Database (13 tables)
- `users` — name, email, password, points, avatar, bio (+ roles via Spatie)
- `courses` — teacher_id, title, description, category, level, is_published, duration_hours
- `lessons` — course_id, title, description, video_url, content, duration_minutes, order, is_published
- `enrollments` — user_id, course_id, progress_percent, completed_at
- `quizzes` — course_id, lesson_id, title, time_limit_minutes, passing_score, is_published
- `questions` — quiz_id, question_text, type, points, order
- `question_options` — question_id, option_text, is_correct, order
- `quiz_attempts` — user_id, quiz_id, score, total_points, passed, started_at, completed_at
- `attempt_answers` — attempt_id, question_id, selected_option_id, is_correct
- `user_progress` — user_id, lesson_id, is_completed, watch_percent, completed_at
- `badges` — name, description, icon, color, criteria_type, criteria_value
- `user_badges` — user_id, badge_id, earned_at
- `discussion_posts` — course_id, user_id, title, body, is_pinned
- `discussion_replies` — post_id, user_id, body

### Models (app/Models/)
- `User` — HasRoles, hasMany(Enrollment, Course, QuizAttempt, UserProgress), belongsToMany(Badge); `avatar_url` computed attribute
- `Course` — belongsTo(User teacher), hasMany(Lesson, Enrollment, Quiz)
- `Lesson` — belongsTo(Course), hasOne(Quiz), hasMany(UserProgress); `embed_url` computed attribute (YouTube/Vimeo)
- `Quiz` — belongsTo(Course, Lesson), hasMany(Question, QuizAttempt)
- `QuizAttempt` — belongsTo(User, Quiz), hasMany(AttemptAnswer); `score_percent` computed attribute
- Other: Question, QuestionOption, AttemptAnswer, Enrollment, UserProgress, Badge, DiscussionPost, DiscussionReply

### Controllers (app/Http/Controllers/)
- `StudentController` — dashboard, courses, enroll, showCourse, watchLesson, markComplete, takeQuiz, submitQuiz, quizResult, leaderboard, discussion, badges
- `TeacherController` — dashboard, courses CRUD, lessons CRUD, quiz CRUD + questions, studentProgress, discussions
- `AdminController` — dashboard, users CRUD, courses management, reports, badges CRUD

### Routes (routes/web.php)
- `/` — Welcome page (redirects to dashboard if logged in)
- `/student/*` — Student routes (middleware: auth, role:student)
- `/teacher/*` — Teacher routes (middleware: auth, role:teacher)
- `/admin/*` — Admin routes (middleware: auth, role:admin)

### Views (resources/views/)
- `layouts/learnhub.blade.php` — Shared sidebar layout (Alpine.js, Tailwind)
- `welcome.blade.php` — Public landing page
- `student/` — dashboard, courses, course-show, lesson, quiz, quiz-result, leaderboard, discussion, badges
- `teacher/` — dashboard, courses, course-create/edit/show, lesson-create/edit, quiz-create/edit, progress, discussions
- `admin/` — dashboard, users, user-create/edit, courses, reports, badges

## Startup
The app starts via `bash start.sh` which:
1. Generates `.env` from environment secrets
2. Clears config/cache
3. Runs `php artisan migrate`
4. Creates storage symlink
5. Starts `php artisan serve --port=5000`

## Points System (Gamification)
- +20 pts — Complete a lesson
- +50 pts — Pass a quiz
- +10 pts — Fail a quiz (participation)
- +5 pts — Post in discussion
- +2 pts — Reply to discussion

## Badge Criteria Types
- `points` — Cumulative point threshold
- `lessons_completed` — Total lessons marked complete
- `courses_completed` — Enrollments with completed_at set
- `enrollments` — Total course enrollments
- `quiz_perfect` — Awarded manually for 100% scores
- `discussions` — Number of posts created

## Demo Accounts (password: `password`)
- **Admin**: admin@learnhub.com
- **Teacher 1**: teacher@learnhub.com (Dr. Sarah Johnson)
- **Teacher 2**: teacher2@learnhub.com (Prof. Michael Chen)
- **Student**: student@learnhub.com (Student User)
- **Top Student**: emma@learnhub.com (Emma Wilson, 920 pts)

## Key Dependencies
- `spatie/laravel-permission` — RBAC
- `alpinejs` — Reactive UI (dropdowns, timer, quiz navigation)
- `tailwindcss` — Styling
- `vite` — Asset bundling
