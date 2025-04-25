<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        if (request()->header('X-Guest-ID') && ! $request->bearerToken()) {
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
                'message' => 'У вас исчерпан срок токена!',
            ], 401);
        }
        $user = $sanctumToken->tokenable;


        $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'delivery_time' => 'required|string|max:255'
        ]);


        $cartItems = DB::table('cart_items')
                        ->where('user_id', $user->id)
                        ->get();
        
        
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста'], 400);
        }

 
        DB::table('orders')->insert([
            'user_id' => $user->id,
            'cart_data' => $cartItems,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'delivery_time' => $request->delivery_time
        ]);
       

        DB::table('cart_items')->where('user_id', $user->id)->delete();
        $order = DB::table('orders')->where('user_id', $user->id)->where('status', 'pending')->get();
        return response()->json($order);
    }


    public function history()
    {
        $user = auth()->user();
        $orders = DB::table('orders')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'История пустая'], 200);
        }

        return response()->json($orders, 200);
    }
}
