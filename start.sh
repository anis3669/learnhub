#!/bin/bash

# Force SQLite and file session — override .env which has MySQL
export DB_CONNECTION=sqlite
export DB_DATABASE=/home/runner/workspace/database/database.sqlite
export SESSION_DRIVER=file
export QUEUE_CONNECTION=sync
export CACHE_STORE=file
export LOG_CHANNEL=single

# Cache config with correct values (bypasses .env on every request)
php artisan config:cache

# Run migrations in case schema changed
php artisan migrate --force --quiet

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=5000
