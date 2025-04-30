<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\OrderCreateRequest;
use App\Models\Api\Cart;
use App\Models\Api\CartItem;
use App\Models\Api\Order;
use Laravel\Sanctum\PersonalAccessToken;


class OrderController extends Controller
{
    public function create(OrderCreateRequest $request)
    {
        if (! request()->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Для оформления заказа необходимо зарегистрироваться!',
            ], 401);
        }

        $token = request()->bearerToken();
        $sanctumToken = PersonalAccessToken::findToken($token);

        if (! $sanctumToken) {
            return response()->json([
                'success' => false,
                'message' => 'У вас истек срок токена!',
            ], 401);
        }


        $user = $sanctumToken->tokenable;
        $cartUser = Cart::where('user_id', $user->id)->first();
        $exists = CartItem::where('cart_id', $cartUser->id)->exists();
        

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Корзина пуста'
            ], 400);
        }

        $data = $request->validated();
        $order = Order::create([
            'user_id' => $user->id,
            'cart_id' => $cartUser->id,
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'delivery_time' => $data['delivery_time'],
        ]);
        

        return response()->json([
            'success' => true,
            'message' => 'Заказ успешно оформлен!',
            'order' => $order,
        ], 200);
    }
}
