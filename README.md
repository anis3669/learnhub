# LearnHub

LearnHub is a Laravel 12 e-learning platform with role-based dashboards, courses, lessons, quizzes, discussions, badges, and a leaderboard.

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+
- A database configured in `.env`

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

## Run

Use the built-in project scripts:

```bash
composer run dev
```

Or start the pieces manually:

```bash
php artisan serve
npm run dev
```

