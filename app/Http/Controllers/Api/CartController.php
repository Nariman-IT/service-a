<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;



class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'assortment_id' => 'required|exists:assortments,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $assortment = DB::table('assortments')->find($request->assortment_id);
  
        // ЛОГИКА GUEST
        if (request()->header('X-Guest-ID') && ! $request->bearerToken()) {
            $cart = $this->guest($assortment->type, $assortment->id, $request->quantity);
        }
       
        // ЛОГИКА USER
        if ($request->bearerToken()) {
            $cart = $this->user($assortment->type, $assortment->id, $request->quantity);
        }
        

        return response()->json([
            'success' => true, 
            'cart' => $cart
        ], 200);
    }



    private function user($assortmentType, $assortmentId, $requestQuantity, )
    {
        $token = request()->bearerToken();
        $sanctumToken = PersonalAccessToken::findToken($token);

        if (! $sanctumToken) {
            return response()->json([
                'success' => false,
                'message' => 'У вас исчерпан срок токена!',
            ], 401);
        }

        $user = $sanctumToken->tokenable;
       
        $total = DB::table('cart_items')
                    ->join('assortments', 'assortment_id', '=', 'assortments.id')
                    ->where('assortments.type', $assortmentType)
                    ->where('cart_items.user_id', $user->id)
                    ->sum('cart_items.quantity');

        $limitCheck = $this->checkLimits($assortmentType, $total, $requestQuantity);
        
        if ($limitCheck) {
            return $limitCheck;
        }


        $currentQty = DB::table('cart_items')
                        ->where('user_id', $user->id)
                        ->where('assortment_id', $assortmentId)
                        ->select('quantity')
                        ->first();

        $currentQty = $currentQty ? $currentQty->quantity : 0;


        $guestCart = DB::table('cart_items')->updateOrInsert(
            ['user_id' => $user->id, 'assortment_id' => $assortmentId],
            ['quantity' => $requestQuantity + $currentQty, 'created_at' => now()]
        );

        return DB::table('cart_items')
                        ->join('assortments', 'assortment_id', '=', 'assortments.id')
                        ->where('user_id', $user->id)
                        ->select('assortments.*', 'cart_items.*')
                        ->get();
    }


    private function guest($assortmentType, $assortmentId, $requestQuantity)
    {
        $guest = DB::table('guests')->where('guest_id',  request()->header('X-Guest-ID'))->first();

        if (! $guest) {
            return response()->json([
                'success' => false,
                'message' => 'У вас исчерпан срок гостевой карты!',
            ], 401);
        }
        $total = DB::table('cart_items')
                    ->join('assortments', 'assortment_id', '=', 'assortments.id')
                    ->where('assortments.type', $assortmentType)
                    ->where('cart_items.guest_id', $guest->id)
                    ->sum('cart_items.quantity');
        
       
        $limitCheck = $this->checkLimits($assortmentType, $total, $requestQuantity);

        if ($limitCheck) {
            return $limitCheck;
        }


        $currentQty = DB::table('cart_items')
                        ->where('guest_id', $guest->id)
                        ->where('assortment_id', $assortmentId)
                        ->select('quantity')
                        ->first();

        $currentQty = $currentQty ? $currentQty->quantity : 0;

        $guestCart = DB::table('cart_items')->updateOrInsert(
            ['guest_id' => $guest->id, 'assortment_id' => $assortmentId],
            ['quantity' => $requestQuantity + $currentQty, 'created_at' => now()]
        );

        return DB::table('cart_items')
                        ->join('assortments', 'assortment_id', '=', 'assortments.id')
                        ->where('guest_id', $guest->id)
                        ->select('assortments.*', 'cart_items.*')
                        ->get();
    }



    private function checkLimits($assortmentType, $currentQuantity, $addingQuantity)
    {
        $limits = [
            'pizza' => 10,
            'drink' => 20,
        ];

        if ($currentQuantity + $addingQuantity > $limits[$assortmentType])
        {
            return response()->json([
                'success' => false,
                'message' => "Максимальное количество $assortmentType: {$limits[$assortmentType]}",
            ], 422);
        }

        return;
    }



}
