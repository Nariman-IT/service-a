<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['user_id', 'cart_data', 'phone', 'email', 'address', 'delivery_time', 'status'];
    protected $casts = ['cart_data' => 'array'];
}
