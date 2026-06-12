<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OperatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(401, 'Необходима авторизация');
        }

        $user = Auth::user();
        
        // Проверяем, что пользователь - оператор или администратор
        if (!$user->isAdmin() && !$user->isOperator()) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }
}
