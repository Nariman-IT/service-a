<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\UserOrderResource;
use App\Models\Api\Order;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function currentOrder()
    {
        $userId = auth()->id();

        $orders = Order::where('user_id', $userId)
                ->where('status', '!=', 'completed')
                ->orderBy('created_at', 'desc')
                ->get();
  
   
        return response()->json([
            'success' => true,
            'orders' => UserOrderResource::collection($orders),
        ], 200);
    }


    public function historyOrder()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
                ->where('status', 'completed')
                ->get();


        return response()->json([
            'success' => true,
            'orders' => UserOrderResource::collection($orders),
           ], 200);
    }
}
