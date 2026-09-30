<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAstrologerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'display_name'    => 'required|string|max:120',
            'email'           => 'required|email|max:255|unique:astrologers,email',
            'phone'           => 'nullable|string|max:25',
            'profile_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'bio'             => 'nullable|string',
            'short_bio'       => 'nullable|string|max:300',
            'specializations' => 'nullable|string|max:500',
            'languages'       => 'nullable|string|max:300',
            'experience_years'=> 'required|integer|min:0|max:99',
            'education'       => 'nullable|string|max:300',
            'chat_rate'       => 'required|numeric|min:0|max:9999',
            'call_rate'       => 'required|numeric|min:0|max:9999',
            'video_rate'      => 'required|numeric|min:0|max:9999',
            'status'          => 'required|in:pending,active,inactive,rejected',
            'is_featured'     => 'nullable|boolean',
            'is_available'    => 'nullable|boolean',
            'service_ids'     => 'nullable|array',
            'service_ids.*'   => 'integer|exists:services,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured'  => $this->boolean('is_featured'),
            'is_available' => $this->boolean('is_available'),
        ]);
    }
}
