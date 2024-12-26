<?php

namespace App\Http\Middleware;

use Closure;
class RoleMiddleware
{
    public function handle($request, Closure $next)
    {


        // Пропускаем маршруты регистрации и авторизации
        if ($request->is('api/register') || $request->is('api/login')) {
            return $next($request);
        }

        // Проверяем права пользователя
        if (!auth()->check()) {
            return response()->json(['message' => 'Пользователь не аутентифицирован'], 401);
        }

        return $next($request);
    }
}
