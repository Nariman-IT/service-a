<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AdminProductCreateRequest;
use App\Http\Requests\Api\Admin\AdminProductDeleteRequest;
use App\Http\Requests\Api\Admin\AdminProductUpdateRequest;
use App\Http\Resources\Api\Admin\AdminProductResource;
use App\Models\Api\Product;



class AdminController extends Controller
{
    public function store(AdminProductCreateRequest $request)
    {
        $data = $request->validated();
        $product = Product::create($data);

        return response()->json([
            'success' => true, 
            'message' => 'Продукт успешно добавлен!',
            'products' => AdminProductResource::make($product)], 201);
    }

    

    public function update(AdminProductUpdateRequest $request)
    {
        $data = $request->validated();
        $product = Product::find($data['id']);
        unset($data['id']);

        $product->update($data);
        
        return response()->json([
            'success' => true, 
            'message' => 'Продукт успешно обновлен!',
            'product' => AdminProductResource::make($product),
        ], 200);
    }



    public function delete(AdminProductDeleteRequest $request)
    {
        $data = $request->validated();
        Product::destroy($data['id']);
        
        return response()->json([
            'success' => true, 
            'message' => 'Продукт успешно удален!',
        ], 200 );
    }

}
