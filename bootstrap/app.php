<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Увеличиваем лимиты для загрузки файлов
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('memory_limit', '512M');

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'operator' => \App\Http\Middleware\OperatorMiddleware::class,
        ]);
        
        // Отключаем ValidatePostSize middleware для веб-роутов
        $middleware->remove(\Illuminate\Http\Middleware\ValidatePostSize::class, 'web');
    })    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
