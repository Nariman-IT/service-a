<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'type', 'description', 'image_url', 'discount'];


    protected static function booted()
    {
        static::updated(function ($product) {
            Cache::forget('products');
        });

        static::created(function ($product) {
            Cache::forget('products');
        });

        static::deleted(function ($product) {
            Cache::forget('products');
        });
    }
}
