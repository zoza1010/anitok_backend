<?php

/**
 * Простой скрипт для тестирования API с локального компьютера
 * 
 * Использование: php test-api-local.php
 */

$baseUrl = 'https://anitok-backend-2.onrender.com';

echo "🧪 Тестирование API: {$baseUrl}\n\n";

// Тест 1: Проверка доступности
echo "1️⃣ Проверка доступности...\n";
$response = file_get_contents($baseUrl . '/api/test');
if ($response !== false) {
    echo "✅ API доступен\n";
    $data = json_decode($response, true);
    if ($data) {
        echo "   Сообщение: {$data['message']}\n";
        echo "   Время: {$data['timestamp']}\n";
        echo "   Окружение: {$data['environment']}\n";
    }
} else {
    echo "❌ API недоступен\n";
    exit(1);
}
echo "\n";

// Тест 2: Регистрация пользователя
echo "2️⃣ Тест регистрации...\n";
$registerData = [
    'name' => 'Test User',
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/auth/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($registerData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Для тестирования

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Ошибка CURL: {$error}\n";
} elseif ($httpCode === 201 || $httpCode === 200) {
    echo "✅ Регистрация работает (HTTP {$httpCode})\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['user']['email'])) {
        echo "   Пользователь создан: {$responseData['user']['email']}\n";
    }
} else {
    echo "❌ Регистрация не работает (HTTP {$httpCode})\n";
    echo "   Ответ: {$response}\n";
}
echo "\n";

// Тест 3: Вход пользователя
echo "3️⃣ Тест входа...\n";
$loginData = [
    'email' => $registerData['email'],
    'password' => $registerData['password']
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Вход работает\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['token'])) {
        echo "   Токен получен\n";
        $token = $responseData['token'];
    } else {
        echo "❌ Токен не получен\n";
        $token = null;
    }
} else {
    echo "❌ Вход не работает (HTTP {$httpCode})\n";
    echo "   Ответ: {$response}\n";
    $token = null;
}
echo "\n";

// Тест 4: Защищенный роут
if ($token) {
    echo "4️⃣ Тест защищенного роута...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/auth/protected-test');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo "✅ Защищенный роут работает\n";
        $responseData = json_decode($response, true);
        if (isset($responseData['message'])) {
            echo "   Сообщение: {$responseData['message']}\n";
        }
    } else {
        echo "❌ Защищенный роут не работает (HTTP {$httpCode})\n";
        echo "   Ответ: {$response}\n";
    }
    echo "\n";
}

// Тест 5: Неавторизованный доступ
echo "5️⃣ Тест неавторизованного доступа...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/auth/protected-test');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 401) {
    echo "✅ Неавторизованный доступ правильно отклонен\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['message'])) {
        echo "   Сообщение: {$responseData['message']}\n";
    }
} else {
    echo "❌ Неавторизованный доступ не обработан правильно (HTTP {$httpCode})\n";
    echo "   Ответ: {$response}\n";
}
echo "\n";

echo "🎉 Тестирование завершено!\n";
echo "API URL: {$baseUrl}\n"; 