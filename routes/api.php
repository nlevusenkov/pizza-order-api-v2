<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;

// Маршруты аутентификации

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/check-token', function (Request $request) {
    return $request->user(); // Возвращает пользователя, если он аутентифицирован
});

Route::middleware(['auth:sanctum', RoleMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::post('/assortment', [AdminController::class, 'addAssortment']);
    Route::post('/assortment/{id}/characteristic', [AdminController::class, 'addCharacteristic']);
    Route::delete('/assortment/{id}', [AdminController::class, 'deleteAssortment']);
    Route::delete('/characteristic/{id}', [AdminController::class, 'deleteCharacteristic']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
});

Route::middleware('auth:sanctum')->post('/orders', [OrderController::class, 'createOrder']); //создание заказа
Route::middleware(['auth:sanctum'])->put('/orders/{orderId}', [OrderController::class, 'updateOrder']);
Route::middleware('auth:sanctum')->delete('/orders/{orderId}/cancel', [OrderController::class, 'cancelOrder']);
Route::middleware('auth:sanctum')->put('/orders/{orderId}/status', [OrderController::class, 'changeOrderStatus']);
Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'GetOrders']);

/*
// Маршруты для официанта

Route::middleware(['auth:sanctum', 'role:waiter'])->group(function () {
    // Просмотр всех заказов
    Route::get('/waiter/orders', [WaiterController::class, 'viewOrders']);

    // Просмотр конкретного заказа
    Route::get('/waiter/orders/{id}', [WaiterController::class, 'viewOrder']);

    // Обновление статуса заказа
    Route::put('/waiter/orders/{id}/status', [WaiterController::class, 'updateOrderStatus']);
});

// Маршруты для пользователя
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/orders', [UserController::class, 'createOrder']);
    Route::get('/orders/{id}', [UserController::class, 'viewOrder']);
    // Другие маршруты для пользователя
});*/


