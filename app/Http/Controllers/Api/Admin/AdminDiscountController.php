<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AdminDiscountRemoveRequest;
use App\Http\Requests\Api\Admin\AdminDiscountSetRequest;
use App\Http\Resources\Api\Admin\AdminProductResource;
use App\Models\Api\Product;


class AdminDiscountController extends Controller
{
    public function setDiscount(AdminDiscountSetRequest $request)
    {
        $data = $request->validated();
        $product = Product::find($data['id']);

        unset($data['id']);
        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Скидка успешно установлена!',
            'product' => AdminProductResource::make($product),
        ], 200);
    }


    public function removeDiscount(AdminDiscountRemoveRequest $request)
    {
        $data = $request->validated();
        $product = Product::find($data['id']);

        if (! $product->discount) {
            return response()->json([
                'success' => false,
                'message' => 'На данном продукте уже отсутствует скидка!',
                'product' => AdminProductResource::make($product),
            ], 409); 
        }

        $product->update(['discount' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Скидка успешно удалена!',
            'product' => AdminProductResource::make($product),
        ], 200);
    }
}
