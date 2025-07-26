# Используем PHP CLI 8.2 образ
FROM php:8.2-cli

# Устанавливаем системные зависимости и расширения для Laravel + pgsql
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_pgsql zip bcmath

# Устанавливаем composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Копируем весь проект
COPY . .

# Устанавливаем PHP-зависимости без dev
RUN composer install --no-dev --optimize-autoloader

# Настроим права для storage и cache
RUN chown -R www-data:www-data storage bootstrap/cache || true

# Экспонируем порт 8000 (тот, что используешь в artisan serve)
EXPOSE 8000

# Запускаем приложение командой artisan serve на 0.0.0.0:8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
