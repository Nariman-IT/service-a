<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
                'id' => $this->id,
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'discount' => $this->discount, 
                'image_url' => $this->image_url,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
    }
}
