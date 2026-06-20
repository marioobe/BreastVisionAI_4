FROM php:8.2-fpm

ARG USER=www-data

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install && npm run build \
    && php artisan key:generate \
    && php artisan storage:link \
    && chown -R ${USER}:${USER} /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
