<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminProductCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|integer',
            'type' => 'required|string|in:pizza,drink',
            'description' => 'required|string|max:500',
            'image_url' => 'required|string|max:255',
        ];
    }
}
