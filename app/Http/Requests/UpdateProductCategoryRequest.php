<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name'        => 'required|string|max:120|unique:product_categories,name,' . $categoryId,
            'description' => 'nullable|string|max:1000',
            'icon'        => 'nullable|string|max:60',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'sort_order'  => $this->input('sort_order', 0),
        ]);
    }
}
