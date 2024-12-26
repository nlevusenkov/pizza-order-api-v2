<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Регистрация нового пользователя

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                    function ($attribute, $value, $fail) {
                        if (preg_match('/[^a-zA-Z0-9@._-]/', $value)) {
                            $fail('Некорректный формат e-mail.');
                        }
                    }
                ],
                'password' => 'required|string|min:6|confirmed',
                'roles' => 'required|array',
                'roles.*' => 'string|exists:roles,name',
            ],
                [
                    'name.required' => 'Имя обязательно для заполнения.',
                    'name.string' => 'Имя должно быть строкой.',
                    'name.max' => 'Имя не должно превышать 100 символов.',
                    'email.required' => 'E-mail обязателен для заполнения.',
                    'email.email' => 'Некорректный формат e-mail.',
                    'email.unique' => 'Этот e-mail уже зарегистрирован.',
                    'password.required' => 'Пароль обязателен для заполнения.',
                    'password.min' => 'Пароль должен содержать не менее 6 символов.',
                    'password.confirmed' => 'Подтверждение пароля не совпадает.',
                ]);

            // Создание пользователя
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Присвоение ролей
            $roles = Role::whereIn('name', $request->roles)->get();
            $user->roles()->attach($roles);

            return response()->json([
                'result' => 201,
                'message' => 'Пользователь успешно зарегистрирован',
                'user' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'result' => 422,
                'message' => 'Ошибка валидации данных',
                'errors' => $e->errors()
            ], 422);

        }
    }


    // Вход пользователя
    public function login(Request $request)
    {
        // Валидация входящих данных
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Получение пользователя по email
        $user = User::where('email', $request->email)->first();

        // Проверка наличия пользователя и правильности пароля
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Возвращаем ошибку 401 если данные неверные
            return response()->json([
                'message' => 'Неверные логин или пароль.',
            ], 401);  // Код ошибки 401
        }

        // Создание токена для успешного входа
        $token = $user->createToken('auth_token')->plainTextToken;
        // Получаем роль пользователя (предполагаем, что это связь "многие ко многим")
        $role = $user->roles->first()->name ?? 'user';  // Получаем роль, если она есть, иначе по умолчанию "user"

        // Возвращаем успешный ответ с токеном
        return response()->json([
            'message' => 'Успешный вход',
            'access_token' => $token,
            'role' => $role,
            'token_type' => 'Bearer',

        ], 200);  // Код ответа 200 для успешной аутентификации
    }

    // Выход пользователя
    public function logout(Request $request)
    {
        // Удаление токена
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Успешный выход',
        ], 200);
    }
}

