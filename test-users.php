<?php

/**
 * Простой скрипт для просмотра пользователей
 */

$baseUrl = 'https://anitok-backend-2.onrender.com';

echo "👥 Просмотр пользователей\n\n";

// Получаем список пользователей
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    echo "✅ Пользователи получены\n";
    echo "Всего пользователей: {$data['total']}\n\n";
    
    if ($data['total'] > 0) {
        echo "Список пользователей:\n";
        echo str_repeat('-', 50) . "\n";
        
        foreach ($data['users'] as $user) {
            echo "ID: {$user['id']}\n";
            echo "Имя: {$user['name']}\n";
            echo "Email: {$user['email']}\n";
            echo "Создан: {$user['created_at']}\n";
            echo str_repeat('-', 50) . "\n";
        }
    } else {
        echo "Пользователей пока нет\n";
    }
} else {
    echo "❌ Ошибка получения пользователей (HTTP {$httpCode})\n";
    echo "Ответ: {$response}\n";
} 