<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAstrologerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function attributes(): array
    {
        return [
            'referral_code' => 'referral code',
        ];
    }

    public function messages(): array
    {
        return [
            'referral_code.regex' => 'The referral code format is invalid.',
        ];
    }

    public function rules(): array
    {
        $astrologerId = $this->route('astrologer')?->id;

        return [
            'display_name'    => 'required|string|max:120',
            'email'           => 'required|email|max:255|unique:astrologers,email,' . $astrologerId,
            'phone'           => 'nullable|string|max:25',
            'profile_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'bio'             => 'nullable|string',
            'short_bio'       => 'nullable|string|max:300',
            'specializations' => 'nullable|string|max:500',
            'languages'       => 'nullable|string|max:300',
            'experience_years'=> 'required|integer|min:0|max:99',
            'education'       => 'nullable|string|max:300',
            'referral_code'   => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9]+$/', 'unique:astrologers,referral_code,' . $astrologerId],
            'chat_rate'       => 'required|numeric|min:0|max:9999',
            'call_rate'       => 'required|numeric|min:0|max:9999',
            'video_rate'      => 'required|numeric|min:0|max:9999',
            'status'          => 'required|in:pending,active,inactive,rejected',
            'rejection_reason'=> 'nullable|string|max:500',
            'is_featured'     => 'nullable|boolean',
            'is_available'    => 'nullable|boolean',
            'service_ids'     => 'nullable|array',
            'service_ids.*'   => 'integer|exists:services,id',
            'availability'    => 'nullable|array',
            'availability.*.day_of_week' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'availability.*.start_time'  => 'nullable|string|max:10',
            'availability.*.end_time'    => 'nullable|string|max:10',
            'availability.*.is_active'   => 'nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [
            'is_featured'  => $this->boolean('is_featured'),
            'is_available' => $this->boolean('is_available'),
        ];

        if ($this->has('referral_code')) {
            $data['referral_code'] = $this->filled('referral_code')
                ? strtoupper(trim($this->input('referral_code')))
                : null;
        }

        $this->merge($data);
    }
}
