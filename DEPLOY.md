# 🚀 Инструкция по деплою на Render

## Шаг 1: Подготовка репозитория

### 1.1 Создание GitHub репозитория

1. Зайди на [GitHub](https://github.com)
2. Создай новый репозиторий `anitok-backend`
3. **НЕ** инициализируй с README (у нас уже есть)

### 1.2 Загрузка кода

```bash
# В папке проекта
git init
git add .
git commit -m "Initial commit: Laravel 12 API with Sanctum"

# Добавляем удаленный репозиторий
git remote add origin https://github.com/YOUR_USERNAME/anitok-backend.git
git branch -M main
git push -u origin main
```

## Шаг 2: Настройка Render

### 2.1 Создание аккаунта

1. Зайди на [render.com](https://render.com)
2. Зарегистрируйся через GitHub
3. Подтверди email

### 2.2 Создание Web Service

1. Нажми **"New +"** → **"Web Service"**
2. Подключи GitHub репозиторий `anitok-backend`
3. Настрой параметры:

**Основные настройки:**
- **Name:** `anitok-api`
- **Environment:** `Docker`
- **Region:** `Frankfurt` (ближе к Украине)
- **Branch:** `main`
- **Root Directory:** (оставь пустым)

**Build & Deploy:**
- **Build Command:** (оставь пустым - используем Dockerfile)
- **Start Command:** `php artisan serve --host=0.0.0.0 --port=8000`

### 2.3 Переменные окружения

Добавь следующие переменные:

```env
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
DB_CONNECTION=pgsql
```

**Для генерации APP_KEY:**
```bash
php artisan key:generate --show
```

## Шаг 3: Создание базы данных

### 3.1 PostgreSQL Database

1. В Render нажми **"New +"** → **"PostgreSQL"**
2. Настрой параметры:
   - **Name:** `anitok-db`
   - **Region:** `Frankfurt`
   - **Database:** `anitok`
   - **User:** (оставь по умолчанию)

### 3.2 Подключение к Web Service

1. Зайди в настройки Web Service
2. В разделе **"Environment Variables"** добавь:

```env
DB_HOST=YOUR_DB_HOST
DB_PORT=YOUR_DB_PORT
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD
```

**Где взять эти значения:**
- Зайди в настройки PostgreSQL базы данных
- Скопируй значения из раздела **"Connections"**

## Шаг 4: Деплой

### 4.1 Первый деплой

1. Нажми **"Create Web Service"**
2. Render автоматически:
   - Соберет Docker образ
   - Установит зависимости
   - Запустит приложение

### 4.2 Проверка логов

Если деплой не удался:
1. Зайди в **"Logs"** в настройках сервиса
2. Проверь ошибки в логах
3. Исправь проблемы и сделай новый коммит

## Шаг 5: Миграции

### 5.1 Запуск миграций

После успешного деплоя нужно запустить миграции:

1. Зайди в **"Shell"** в настройках Web Service
2. Выполни команды:

```bash
php artisan migrate --force
```

### 5.2 Проверка API

Протестируй API endpoints:

```bash
# Регистрация
curl -X POST https://your-app.onrender.com/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password"}'

# Вход
curl -X POST https://your-app.onrender.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

## Шаг 6: Настройка домена (опционально)

### 6.1 Кастомный домен

1. В настройках Web Service зайди в **"Settings"**
2. В разделе **"Custom Domains"** добавь свой домен
3. Настрой DNS записи

## 🔧 Решение проблем

### Проблема: "Route [login] not defined"

**Решение:** Уже исправлено в `app/Exceptions/Handler.php`

### Проблема: База данных не подключается

**Решение:**
1. Проверь переменные окружения
2. Убедись, что PostgreSQL создан в том же регионе
3. Проверь логи подключения

### Проблема: APP_KEY не установлен

**Решение:**
```bash
php artisan key:generate --show
```
Скопируй результат в переменную `APP_KEY`

### Проблема: Миграции не запускаются

**Решение:**
1. Проверь подключение к БД
2. Запусти миграции через Shell:
```bash
php artisan migrate --force
```

## 📞 Поддержка

Если возникли проблемы:
1. Проверь логи в Render
2. Убедись, что все переменные окружения установлены
3. Проверь подключение к базе данных

## 🎉 Готово!

После успешного деплоя твой API будет доступен по адресу:
`https://your-app-name.onrender.com`

**Примеры URL:**
- `https://your-app-name.onrender.com/api/auth/register`
- `https://your-app-name.onrender.com/api/auth/login`
- `https://your-app-name.onrender.com/api/user` 