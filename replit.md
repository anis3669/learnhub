# LearnHub - Laravel Application

## Overview
A Laravel 12 web application with Breeze authentication scaffolding and Spatie Laravel Permission for role-based access control.

## Tech Stack
- **Backend**: PHP 8.2 + Laravel 12
- **Frontend**: Vite + Tailwind CSS + Alpine.js
- **Database**: PostgreSQL (Replit built-in)
- **Auth**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission v6

## Project Structure
- `app/` - Application code (Controllers, Models, Providers)
- `bootstrap/app.php` - Application bootstrap with trusted proxy config
- `database/migrations/` - Database migrations
- `resources/views/` - Blade templates
- `routes/` - Route definitions
- `public/build/` - Compiled frontend assets

## Running the App
The app starts via `bash start.sh` which:
1. Generates `.env` from environment variables
2. Clears config/cache
3. Runs migrations
4. Starts Laravel on port 5000 via `php artisan serve`

## Environment Variables
- `APP_KEY` - Laravel application key (stored as shared env var)
- `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD` - PostgreSQL credentials (Replit managed)
- `APP_NAME` - Application name

## Workflow
- **Start application**: `bash start.sh` → serves on port 5000

## Deployment
Configured for autoscale deployment. Build runs `composer install` and `npm run build`.
