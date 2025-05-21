<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Role;


class UserController extends Controller
{
    // Создание нового заказа
    public function createOrder(Request $request)
    {
        $request->validate([
            'pizza_id' => 'required|integer|exists:pizzas,id',
            'quantity' => 'required|integer|min:1',
            // Добавьте другие необходимые поля
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'pizza_id' => $request->pizza_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            // Добавьте другие поля
        ]);

        return response()->json([
            'message' => 'Заказ создан успешно',
            'order' => $order,
        ], 201);
    }

    // Просмотр заказа
    public function viewOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        return response()->json(['order' => $order], 200);
    }
}

