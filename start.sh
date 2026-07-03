#!/bin/bash

# Use values from .env (MySQL/XAMPP on local Windows setup).
# Avoid forcing SQLite here because it breaks DB/session consistency.
php artisan optimize:clear

# Run migrations in case schema changed
php artisan migrate --force --quiet

# Start Laravel server
php artisan serve --host=127.0.0.1 --port=8000
