<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'category_id'       => 'required|integer|exists:product_categories,id',
            'name'              => 'required|string|max:180',
            'sku'               => 'nullable|string|max:64|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0.01|max:999999.99',
            'sale_price'        => 'nullable|numeric|min:0.01|lt:price|max:999999.99',
            'stock'             => 'required|integer|min:0',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status'            => 'required|in:active,inactive,out_of_stock',
            'is_featured'       => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sale_price.lt' => 'Sale price must be lower than the standard regular price.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
