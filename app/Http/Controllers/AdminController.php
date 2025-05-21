<?php

namespace App\Http\Controllers;

use App\Models\Assortment;
use App\Models\Characteristic;
use Illuminate\Http\Request;
use App\Models\User;


class AdminController extends Controller
{
    // Метод для отображения панели администратора
    public function dashboard()
    {
        return response()->json(['message' => 'Welcome to admin dashboard']);
    }

    // Метод для получения всех пользователей
    public function getUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Метод для удаления пользователя
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }



    // Метод для добавления ассортимента
    public function addAssortment(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $assortment = Assortment::create($request->only(['name', 'description', 'price']));

        return response()->json([
            'message' => 'Продукт добавлен',
            'assortment' => $assortment,
        ], 201);
    }

    // Метод для добавления характеристики
    public function addCharacteristic(Request $request, $assortmentId)
    {
        $request->validate([
            'name' => 'required|string',
            'value' => 'required|string',
        ]);

        $assortment = Assortment::findOrFail($assortmentId);

        $characteristic = new Characteristic($request->only(['name', 'value']));
        $assortment->characteristics()->save($characteristic);

        return response()->json([
            'message' => 'Характеристика добавлена',
            'characteristic' => $characteristic,
        ], 201);
    }

    // Метод для удаления ассортимента
    public function deleteAssortment($id)
    {
        $assortment = Assortment::findOrFail($id);
        $assortment->delete();

        return response()->json(['message' => 'Продукт удален'], 200);
    }

    // Метод для удаления характеристики
    public function deleteCharacteristic($id)
    {
        $characteristic = Characteristic::findOrFail($id);
        $characteristic->delete();

        return response()->json(['message' => 'Характеристика удалена'], 200);
    }
}
