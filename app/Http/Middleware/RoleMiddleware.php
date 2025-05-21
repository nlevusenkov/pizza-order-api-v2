<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Пропускаем общедоступные маршруты
        if ($request->is('api/register') || $request->is('api/login')) {
            return $next($request);
        }

        // Проверяем аутентификацию
        if (!Auth::check()) {
            return response()->json(['message' => 'Пользователь не аутентифицирован'], 401);
        }

        // Получаем текущего пользователя
        $user = Auth::user();

        // Проверяем роли пользователя
        $userRoles = $user->roles->pluck('name')->toArray();

        // Если требуются конкретные роли
        if (!empty($roles)) {
            $hasAccess = false;
            foreach ($roles as $role) {
                if (in_array($role, $userRoles)) {
                    $hasAccess = true;
                    break;
                }
            }

            if (!$hasAccess) {
                return response()->json([
                    'message' => 'Недостаточно прав для выполнения действия',
                    'required_roles' => $roles,
                    'user_roles' => $userRoles
                ], 403);
            }
        }

        return $next($request);
    }
}
