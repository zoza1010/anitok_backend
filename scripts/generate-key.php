<?php

/**
 * Скрипт для генерации APP_KEY для деплоя на Render
 * 
 * Использование:
 * php scripts/generate-key.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Генерируем новый ключ
$key = base64_encode(random_bytes(32));

echo "APP_KEY для Render:\n";
echo "base64:" . $key . "\n\n";

echo "Скопируй этот ключ в переменную окружения APP_KEY в Render\n";
echo "Или добавь в .env файл:\n";
echo "APP_KEY=base64:" . $key . "\n"; 