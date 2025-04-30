<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\CartProductAddRequest;
use App\Http\Resources\Api\Cart\CartResource;
use App\Models\Api\Cart;
use App\Models\Api\Guest;
use App\Models\Api\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;


class CartController extends Controller
{
    public function add(CartProductAddRequest $request)
    {
        $data = $request->validated();
        $product = Product::find($data['product_id']);

        // ЛОГИКА USER
        if ($request->bearerToken()) {
            $cart = $this->user($product->type, $product->id, $data['quantity']);
       
            if ($cart instanceof JsonResponse) {
                return $cart;
            }
        }


        // ЛОГИКА GUEST
        if (request()->header('X-Guest-ID')) {
            $cart = $this->guest($product->type, $product->id, $data['quantity']);
            
            if ($cart instanceof JsonResponse) {
                return $cart;
            }
        }
       

        return response()->json([
            'success' => true, 
            'cart' => CartResource::collection($cart),
        ], 200);
    }



    private function user($productType, $productId, $productQuantity)
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
        $cart = Cart::where('user_id', $user->id)->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
            ]);
        }
        
        $count = DB::table('cart_items')
                    ->join('products', 'product_id', '=', 'products.id')
                    ->where('products.type', $productType)
                    ->where('cart_items.cart_id', $cart->id)
                    ->sum('cart_items.quantity');


        $checkLimits = $this->checkLimits($productType, $count, $productQuantity);
        
        if ($checkLimits) {
            return $checkLimits;
        }


        $currentQuantity = DB::table('cart_items')
                        ->where('cart_id', $cart->id)
                        ->where('product_id', $productId)
                        ->select('quantity')
                        ->first();


        $currentQuantity = $currentQuantity ? $currentQuantity->quantity : 0;


        DB::table('cart_items')->updateOrInsert(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            ['quantity' => $productQuantity + $currentQuantity, 'created_at' => now()]
        );


        $cart = DB::table('cart_items')
                    ->join('products', 'product_id', '=', 'products.id')
                    ->where('cart_id', $cart->id)
                    ->select('products.*', 'cart_items.*')
                    ->get();

        return $cart;
    }


    private function guest($productType, $productId, $productQuantity)
    {
        $guest = Guest::where('guest_id', request()->header('X-Guest-ID'))->first();
        
        if (! $guest) {
            return response()->json([
                'success' => false,
                'message' => 'У вас исчерпан срок гостевой карты!',
            ], 401);
        }

        $cart = Cart::where('guest_id', $guest->id)->first();

        if (! $cart) {
            $cart = Cart::create([
                'guest_id' => $guest->id,
            ]);
        }

        $count = DB::table('cart_items')
                    ->join('products', 'product_id', '=', 'products.id')
                    ->where('products.type', $productType)
                    ->where('cart_items.cart_id', $cart->id)
                    ->sum('cart_items.quantity');
       
       
        $limitCheck = $this->checkLimits($productType, $count, $productQuantity);

        if ($limitCheck) {
            return $limitCheck;
        }



        $currentQuantity = DB::table('cart_items')
                        ->where('cart_id', $cart->id)
                        ->where('product_id', $productId)
                        ->select('quantity')
                        ->first();


        $currentQuantity = $currentQuantity ? $currentQuantity->quantity : 0;



        DB::table('cart_items')->updateOrInsert(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            ['quantity' => $productQuantity + $currentQuantity, 'created_at' => now()]
        );


        $cart = DB::table('cart_items')
                    ->join('products', 'product_id', '=', 'products.id')
                    ->where('cart_id', $cart->id)
                    ->select('products.*', 'cart_items.*')
                    ->get();

        return $cart;
    }




    private function checkLimits($productType, $currentQuantity, $addingQuantity)
    {
        $limits = [
            'pizza' => 10,
            'drink' => 20,
        ];

        if ($currentQuantity + $addingQuantity > $limits[$productType])
        {
            return response()->json([
                'success' => false,
                'message' => "Максимальное количество $productType: {$limits[$productType]}",
            ], 422);
        }

        return null;
    }

}
