<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AutoMigrate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, нужно ли запустить миграции
        if (app()->environment('production') && !Schema::hasTable('migrations')) {
            try {
                // Запускаем миграции
                Artisan::call('migrate', ['--force' => true]);
                
                // Очищаем кэш конфигурации
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                
                \Log::info('Auto-migration completed successfully');
            } catch (\Exception $e) {
                \Log::error('Auto-migration failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
} 