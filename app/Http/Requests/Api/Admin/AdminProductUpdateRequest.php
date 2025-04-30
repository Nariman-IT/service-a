<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminProductUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:products,id',
            'name' => 'nullable|string|max:255|unique:products,name',
            'price' => 'nullable|integer',
            'type' => 'nullable|string|in:pizza,drink',
            'description' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:255',
        ];
    }
}
