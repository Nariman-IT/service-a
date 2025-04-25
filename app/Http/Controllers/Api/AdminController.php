<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Order;
use Illuminate\Http\Request;
use App\Models\Api\Assortment;

class AdminController extends Controller
{
    public function store(Request $request)
    {
        $admin = $this->isAdmin();
        if ($admin) return $admin;

        $request->validate([
            'name' => 'required|string|max:255|unique:assortments,name',
            'price' => 'required|integer',
            'type' => 'required|string|in:pizza,drink',
            'description' => 'required|string|max:500',
            'image_url' => 'required|string|max:255',
        ]);

        $assortment = Assortment::create([
            'name' => $request->name,
            'price' => $request->price,
            'type' => $request->type,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);
        
        return response()->json([
            'success' => true, 
            'assortment' => $assortment], 201);
    }

    public function update(Request $request)
    {

        $admin = $this->isAdmin();
        if ($admin) return $admin;

        $validated = $request->validate([
            'id' => 'required|integer|exists:assortments,id',
            'name' => 'nullable|string|max:255|unique:assortments,name',
            'price' => 'nullable|integer',
            'type' => 'nullable|string|in:pizza,drink',
            'description' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:255',
        ]);


        $assortment = Assortment::find($validated['id']);
        unset($validated['id']);

        $assortment->update($validated);
        
        return response()->json([
            'success' => true, 
            'assortment' => $assortment,
        ], 200);
    }

    public function delete(Request $request)
    {
        $admin = $this->isAdmin();
        if ($admin) return $admin;

        $validated = $request->validate([
            'id' => 'required|integer|exists:assortments,id',
        ]);

        Assortment::destroy($validated['id']);
        
        return response()->json([
            'success' => true, 
            'message' => 'Запись успешно удалена!',
        ], 200);
    }

    public function list()
    {
        $admin = $this->isAdmin();
        if ($admin) return $admin;

        $orders = Order::where('status', 'pending')
                    ->orWhere('status', 'in_work')
                    ->get();
    
        if ($orders->isEmpty()){
            return response()->json([
                'success' => true, 
                'message' => 'Нет активных заказов!',
            ], 200);
        }             


        return response()->json([
            'success' => true, 
            'orders' => $orders,
        ], 200);
    }

    public function order(Request $request)
    {
        $admin = $this->isAdmin();
        if ($admin) return $admin;

        $validated = $request->validate([
            'id' => 'required|integer|exists:assortments,id',
            'status' => 'required|string|in:pending,in_work,completed',
        ]);

        $assortment = Order::find($validated['id']);

        if ($assortment->status === 'completed') {
            return response()->json([
                'success' => false, 
                'message' => 'Заказ уже завершен!',
            ], 404);
        }

        unset($validated['id']);

        $assortment->update($validated);
        
        return response()->json([
            'success' => true, 
            'assortment' => $assortment,
        ], 200);

    }



    private function isAdmin()
    {
        $admin = auth()->user();

        if (! $admin->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав!',
            ], 403);
        }

        return null;
    }
}
