# Базовый образ PHP
FROM php:8.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копирование проекта
COPY . .

# Установка зависимостей Laravel
RUN composer install --no-dev --optimize-autoloader

# Права на storage и bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache || true

# Открываем порт
EXPOSE 8000

# Запускаем Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
