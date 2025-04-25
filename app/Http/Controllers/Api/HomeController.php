<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function assortment()
    {

        $assortments = DB::table('assortments')->get();
        $token = request()->bearerToken();
        $guest = request()->header('X-Guest-ID');
        $ip = filter_var(request()->ip(), FILTER_VALIDATE_IP);
        $checkGuest = DB::table('guests')->where('guest_id', $guest)->exists();

        
        if (! $token && ! ($checkGuest ? $guest : null)) {

            $exists = DB::table('guests')->where('ip', $ip)->first();
            if ($exists) {
                return response()->json([
                    'success' => true,
                    'guestToken' => $exists->guest_id,
                    'assortments' => $assortments,
                ], 200);
            }

            $guestToken = Str::uuid()->toString();
            DB::table('guests')->insert(['guest_id' => $guestToken, 'ip' => $ip, 'created_at' => now()]);

            return response()->json([
                'success' => true,
                'guestToken' => $guestToken,
                'assortments' => $assortments,
            ], 200);
        }

        
        return response()->json([
            'success' => true,
            'assortments' => $assortments,
        ], 200);
       
    }



    public function myOrder()
    {
        
        $userId = auth()->id();

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет активных заказов!',
            ], 404);
        }    


        return response()->json([
            'success' => true,
            'orders' => $orders,
        ], 200);
    }

}
