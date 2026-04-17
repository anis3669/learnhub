FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
 git \
 curl \
 unzip \
 libzip-dev \
 libicu-dev \
 && docker-php-ext-install intl zip pdo_mysql

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
 apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
RUN npm install && npm run build

EXPOSE 8080

CMD cp .env.example .env && \
 php artisan key:generate --force && \
 php artisan storage:link && \
 php artisan config:clear && \
 php artisan route:clear && \
 php artisan view:clear && \
 php artisan optimize:clear && \
 php -S 0.0.0.0:${PORT:-8080} -t public
