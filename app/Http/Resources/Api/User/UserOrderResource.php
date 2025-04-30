<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class UserOrderResource extends JsonResource
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
            'products' => UserProductResource::collection(DB::table('cart_items')
                                                                            ->join('products', 'products.id', '=', 'cart_items.product_id')
                                                                            ->where('cart_items.cart_id', $this->cart_id)
                                                                            ->select('products.*', 'cart_items.quantity')
                                                                            ->get()),
        ];
    }
}
