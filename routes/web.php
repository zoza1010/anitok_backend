<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Миграции выполнены успешно!';
});


Route::get('/', function () {
    return view('welcome');
});
