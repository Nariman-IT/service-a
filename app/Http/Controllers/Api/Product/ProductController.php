<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductCategoriesRequest;
use App\Http\Requests\Api\Product\ProductSearchRequest;
use App\Http\Resources\Api\Product\ProductResource;
use App\Models\Api\Guest;
use App\Models\Api\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function product()
    {
        $products = Product::all();


        if (! request()->header('X-Guest-ID') && ! request()->bearerToken()) {
            $ip = filter_var(request()->ip(), FILTER_VALIDATE_IP);
            $guestToken = Str::uuid()->toString();
            Guest::create(['guest_id' => $guestToken, 'ip' => $ip, 'created_at' => now()]);

            return response()->json([
                'success' => true,
                'guestToken' => $guestToken,
                'products' => ProductResource::collection($products),
            ], 200);

        }
        

        return response()->json([
            'success' => true,
            'products' => ProductResource::collection($products),
        ], 200);

    }



    public function categories(ProductCategoriesRequest $request)
    {
        $data = $request->validated();
        $products = Product::where('type', $data['type'])->get();

        return response()->json([
            'success' => true,
            'products' => ProductResource::collection($products),
        ], 200);
    }


    public function search(ProductSearchRequest $request)
    {
        $data = $request->validated();
        $product = Product::where('name', $data['name'])->first();

        return response()->json([
            'success' => true,
            'product' => ProductResource::make($product),
        ], 200);
    }
}
