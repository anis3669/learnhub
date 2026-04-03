#!/bin/bash
set -e

# Generate .env file from environment variables
cat > .env << EOF
APP_NAME=${APP_NAME:-LearnHub}
APP_ENV=${APP_ENV:-local}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME:-LearnHub}"

VITE_APP_NAME="${APP_NAME:-LearnHub}"
EOF

echo ".env file generated"

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# Make storage writable
chmod -R 775 storage bootstrap/cache
php artisan storage:link 2>/dev/null || true

# Start the Laravel server on port 5000
echo "Starting Laravel on port 5000..."
php artisan serve --host=0.0.0.0 --port=5000
