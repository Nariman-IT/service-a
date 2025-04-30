<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AdminAllOrderResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'cart_id' =>$this->cart_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'delivery_time' => $this->delivery_time,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => AdminCartItemResource::collection(DB::table('cart_items')
                                                                ->where('cart_items.cart_id', $this->cart_id)
                                                                ->select('cart_items.*')
                                                                ->get()),
        ];
    }
}
