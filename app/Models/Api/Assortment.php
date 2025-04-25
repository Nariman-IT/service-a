<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assortment extends Model
{
    use HasFactory;
    protected $table = 'assortments';
    protected $fillable = ['name', 'price', 'type', 'description', 'image_url'];
}
