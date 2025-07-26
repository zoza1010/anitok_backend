<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Удалите или закомментируйте 'allowed_origins' => ['*'],
    // 'allowed_origins' => ['*'],

    // Используйте 'allowed_origins_patterns' для разрешения конкретных доменов
    'allowed_origins_patterns' => [
        'http://localhost:8000',        // Ваш бэкенд локально
        'http://localhost:3000',        // Ваш фронтенд локально (пример)
        'http://localhost:3001',        // Другой фронтенд локально (пример)
        'https://anitok.vercel.app',     // Ваш продакшн фронтенд
        'https://anitok-backend-2.onrender.com', // Ваш Render бэкенд
        'http://127.0.0.1:8000',        // Альтернатива localhost:8000
        'http://127.0.0.1:3000',        // Альтернатива localhost:3000
        // Добавьте любые другие домены/порты, которые используются
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 600, // Можно увеличить для продакшена, например, до 600 (10 минут)

    'supports_credentials' => true, // Оставляем true

];