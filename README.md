# LearnHub

LearnHub is a Laravel 12 e-learning platform with role-based dashboards, courses, lessons, quizzes, discussions, badges, and a leaderboard.

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+
- A database configured in `.env`

## Setup

# 1. Copy environment file
cp .env.example .env

# 2. Install dependencies
composer install
npm install

# 3. Generate app key
php artisan key:generate

# 4. Run migrations
php artisan migrate

# 5. (Optional) Seed roles
php artisan db:seed:RoleSeeder

# 6. Start development
composer run dev  # Starts server + queue + vite
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

