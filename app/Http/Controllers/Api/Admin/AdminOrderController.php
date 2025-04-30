<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AdminOrderUpdateStatusRequest;
use App\Http\Resources\Api\Admin\AdminAllOrderResource;
use App\Models\Api\Order;
use App\Enums\OrderStatus;

class AdminOrderController extends Controller
{
    public function listOrder()
    {
        $orders = Order::where('status', OrderStatus::PENDING)
                        ->orWhere('status', OrderStatus::IN_WORK)
                        ->get();
    
        return response()->json([
            'success' => true,
            'orders' => AdminAllOrderResource::collection($orders),
        ], 200);
    }


    
    public function orderUpdate(AdminOrderUpdateStatusRequest $request)
    {
        $data = $request->validated();
        $order = Order::find($data['id']);
       
        if ($order->status === 'completed') {
            return response()->json([
                'success' => false, 
                'message' => 'Заказ уже завершен!',
                'order' => AdminAllOrderResource::make($order),
            ], 404);
        }
        
        unset($data['id']);
        $order->update($data);
        

        return response()->json([
            'success' => true, 
            'message' => 'Статус заказа успешно изменен!',
            'order' => AdminAllOrderResource::make($order),
        ], 200);
    }
}
