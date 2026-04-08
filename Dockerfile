FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
 git \
 curl \
 unzip \
 libzip-dev \
 libicu-dev \
 && docker-php-ext-install intl zip pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Laravel setup
RUN php artisan key:generate
RUN php artisan storage:link

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
