# 🚀 Быстрый деплой на Render

## ✅ Что уже готово

- ✅ Dockerfile для контейнеризации
- ✅ render.yaml для автоматической настройки
- ✅ Исправлена проблема с авторизацией
- ✅ Laravel 12 с Sanctum
- ✅ PostgreSQL конфигурация

## 🎯 Пошаговый деплой

### 1. GitHub репозиторий
```bash
git init
git add .
git commit -m "Initial commit: Laravel 12 API"
git remote add origin https://github.com/YOUR_USERNAME/anitok-backend.git
git push -u origin main
```

### 2. Render настройка
1. Зайди на [render.com](https://render.com)
2. **New +** → **Web Service**
3. Подключи GitHub репозиторий
4. Настрой:
   - **Name:** `anitok-api`
   - **Environment:** `Docker`
   - **Region:** `Frankfurt`

### 3. База данных
1. **New +** → **PostgreSQL**
2. **Name:** `anitok-db`
3. **Region:** `Frankfurt`

### 4. Переменные окружения
В Web Service добавь:
```env
APP_ENV=production
APP_KEY=base64:YOUR_KEY
DB_CONNECTION=pgsql
```

**Генерируй APP_KEY:**
```bash
php scripts/generate-key.php
```

### 5. Деплой
1. Нажми **Create Web Service**
2. Жди завершения сборки
3. Зайди в **Shell** и выполни:
```bash
php artisan migrate --force
```

### 6. Тестирование
```bash
php scripts/test-api.php https://your-app-name.onrender.com
```

## 🔗 Результат

API будет доступен по адресу:
`https://your-app-name.onrender.com`

**Endpoints:**
- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/protected-test`
- `GET /api/user`

## 🆘 Если что-то не работает

1. Проверь логи в Render
2. Убедись, что все переменные окружения установлены
3. Проверь подключение к базе данных
4. Запусти миграции через Shell

## 📞 Поддержка

Подробная инструкция в `DEPLOY.md` 