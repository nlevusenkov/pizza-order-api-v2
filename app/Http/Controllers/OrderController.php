<?php

namespace App\Http\Controllers;

use App\Models\Assortment;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // создание заказа
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:assortments,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;

        foreach ($validated['products'] as $productData) {
            $product = Assortment::find($productData['id']);
            $totalPrice += $product->price * $productData['quantity'];
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_price' => $totalPrice,
        ]);

        foreach ($validated['products'] as $productData) {
            $product = Assortment::find($productData['id']);
            OrderItem::create([
                'order_id' => $order->id,
                'assortment_id' => $product->id,
                'quantity' => $productData['quantity'],
                'price' => $product->price,
            ]);
        }

        return response()->json([
            'message' => 'Заказ успешно создан',
            'order' => $order->load('items.assortment'),
        ]);
    }
    // обновление заказа
    public function updateOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);

//        if (!$order) {
//            return response()->json(['message' => 'Заказ не найден'], 404);
//        }

//        // Проверяем, принадлежит ли заказ текущему пользователю
//        if ($order->user_id !== auth()->id()) {
//            return response()->json(['message' => 'Unauthorized'], 403);
//        }

        $products = $request->input('products');

        if (empty($products)) {
            return response()->json(['message' => 'Продукты не предоставлены'], 400);
        }

        $totalPrice = 0;

        // Удаляем текущий состав заказа
        $order->assortments()->delete();

        // Добавляем обновленные данные
        foreach ($products as $product) {
            $assortmentItem = Assortment::find($product['id']);

            if (!$assortmentItem) {
                return response()->json(['message' => "Товар с идентификатором {$product['id']} не найден"], 404);
            }

            $order->assortments()->create([
                'assortment_id' => $assortmentItem->id,
                'quantity' => $product['quantity'],
                'price' => $assortmentItem->price,
            ]);

            $totalPrice += $assortmentItem->price * $product['quantity'];
        }

        // Обновляем общую сумму
        $order->update(['total_price' => $totalPrice]);

        return response()->json(['message' => 'Заказ успешно обновлен', 'order_id' => $order->id], 200);
    }
    // отмена заказа
    public function cancelOrder($orderId): JsonResponse
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        // Проверяем, принадлежит ли заказ текущему пользователю
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($order->status === 'canceled') {
            return response()->json(['message' => 'Этот заказ уже отменен'], 400);
        }

        // Изменяем статус заказа на "canceled"
        $order->status = 'canceled';
        $order->save();

        return response()->json([
            'message' => 'Заказ отменен',
            'order' => $order
        ]);
    }
    // изменение статуса заказа
    public function changeOrderStatus(Request $request, $orderId): JsonResponse
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        // Проверяем, принадлежит ли заказ текущему пользователю
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Валидация нового статуса
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,canceled,shipped', // Возможные статусы
        ]);

        // Пример ограничений на изменение статуса
        if ($order->status === 'completed' && $validated['status'] !== 'completed') {
            return response()->json(['message' => 'Вы не можете изменить статус завершенного заказа'], 400);
        }

        // Если статус не прошел валидацию, возвращаем ошибку
        if (!in_array($validated['status'], ['pending', 'completed', 'canceled', 'shipped'])) {
            return response()->json(['message' => 'Неверный статус заказа'], 400);
        }

        // Обновляем статус
        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'message' => 'Статус заказа обновлен',
            'order' => $order
        ]);
    }

    public function getOrders(): JsonResponse
    {
        // Получаем все заказы, которые принадлежат текущему пользователю
        $orders = Order::where('user_id', auth()->id())->get();

        // Если заказов нет
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'У вас нет заказов'], 404);
        }

        // Возвращаем список заказов
        return response()->json([
            'orders' => $orders
        ]);
    }

    // Просмотр подробной информации о заказе
    public function getOrder($orderId): JsonResponse
    {
        $order = Order::with('items.assortment') // Загрузка товаров, входящих в заказ
        ->where('user_id', auth()->id()) // Проверка, что заказ принадлежит текущему пользователю
        ->find($orderId);

        // Если заказ не найден
        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        // Возвращаем подробную информацию о заказе
        return response()->json([
            'order' => $order
        ]);
    }




}
