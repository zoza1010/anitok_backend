# Используем официальный PHP образ с FPM
FROM php:8.2-fpm

# Установка зависимостей для Laravel и PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring

# Установка Composer (последняя версия)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копируем файлы проекта
COPY . .

# Установка PHP-зависимостей Laravel
RUN composer install --no-dev --optimize-autoloader

# Права на storage и bootstrap/cache (важно для кэша и логов)
RUN chmod -R 775 storage bootstrap/cache || true

# Скрипт запуска: сначала миграции, потом сервер
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000

# Открываем порт 8000
EXPOSE 8000
