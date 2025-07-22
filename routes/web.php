<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Route::get('/db-test', function () {
    try {
        $result = DB::select('SELECT NOW()');
        return ['status' => 'ok', 'time' => $result[0]->now];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
});
Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    return 'Кеши очищены и конфиги закешированы';
});

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Миграции выполнены успешно!';
});


Route::get('/', function () {
    return view('welcome');
});
