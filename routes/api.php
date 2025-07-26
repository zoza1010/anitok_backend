<?php

use App\Http\Controllers\Auth\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Простой тестовый роут для проверки работы API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API работает!',
        'timestamp' => now(),
        'environment' => app()->environment()
    ]);
});




Route::prefix('/auth')->middleware(['throttle:api'])->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function(){
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::get('/protected-test', function (Request $request) {
            return response()->json([
                'message' => 'Вы успешно авторизованы!',
                
            ]);
        });
    });
});


