# AniTok Backend API

Laravel 12 API backend для проекта AniTok с аутентификацией через Sanctum.

## 🚀 Технологии

- **Laravel 12** - PHP фреймворк
- **Laravel Sanctum** - API аутентификация
- **PostgreSQL** - база данных
- **Docker** - контейнеризация
- **Render** - хостинг

## 📋 API Endpoints

### Аутентификация
- `POST /auth/register` - регистрация
- `POST /auth/login` - вход
- `POST /auth/logout` - выход (требует авторизации)
- `GET /auth/protected-test` - тест защищенного роута

### Пользователи
- `GET /user` - получить данные текущего пользователя

## 🛠 Локальная разработка

```bash
# Установка зависимостей
composer install

# Копирование .env
cp .env.example .env

# Генерация ключа приложения
php artisan key:generate

# Настройка базы данных в .env
# DB_CONNECTION=pgsql
# DB_HOST=localhost
# DB_PORT=5432
# DB_DATABASE=anitok
# DB_USERNAME=postgres
# DB_PASSWORD=password

# Миграции
php artisan migrate

# Запуск сервера
php artisan serve
```

## 🚀 Деплой на Render

### 1. Подготовка репозитория

```bash
# Инициализация git (если еще не сделано)
git init
git add .
git commit -m "Initial commit"

# Создание репозитория на GitHub
# Затем:
git remote add origin https://github.com/your-username/anitok-backend.git
git push -u origin main
```

### 2. Настройка на Render

1. Зайди на [render.com](https://render.com)
2. Создай новый **Web Service**
3. Подключи GitHub репозиторий
4. Настрой переменные окружения:

```env
APP_ENV=production
APP_KEY=base64:your-generated-key
DB_CONNECTION=pgsql
```

### 3. Создание базы данных

1. В Render создай **PostgreSQL** базу данных
2. Назови её `anitok-db`
3. Render автоматически подключит её к сервису

### 4. Переменные окружения

Render автоматически настроит переменные базы данных из `render.yaml`:

- `DB_HOST` - из базы данных
- `DB_PORT` - из базы данных  
- `DB_DATABASE` - из базы данных
- `DB_USERNAME` - из базы данных
- `DB_PASSWORD` - из базы данных

### 5. Деплой

После настройки Render автоматически:
- Соберет Docker образ
- Запустит миграции
- Развернет приложение

## 🔧 Исправление проблем

### Проблема с авторизацией

Если получаешь ошибку `Route [login] not defined` при невалидном токене, добавь в `app/Exceptions/Handler.php`:

```php
use Illuminate\Auth\AuthenticationException;

protected function unauthenticated($request, AuthenticationException $exception)
{
    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Ви не авторизовані.',
        ], 401);
    }

    return redirect()->guest(route('login'));
}
```

## 📝 Структура проекта

```
anitok-backend/
├── app/
│   ├── Http/Controllers/Auth/
│   │   └── AuthController.php
│   └── Models/
├── config/
├── database/
├── routes/
│   └── api.php
├── Dockerfile
├── render.yaml
└── composer.json
```

## 🔗 Полезные ссылки

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Render Documentation](https://render.com/docs)

## 📄 Лицензия

MIT License
