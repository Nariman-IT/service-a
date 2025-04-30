<?php

namespace App\Http\Resources\Api\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'type' => $this->type,
            'description' => $this->description,
            'image_url '=> $this->image_url,
            'discount '=> $this->discount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
        ];
    }
}
