<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('web')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'astrologer_id'     => 'required|integer|exists:astrologers,id',
            'service_id'        => 'nullable|integer|exists:services,id',
            'booking_date'      => 'required|date|after_or_equal:today',
            'start_time'        => 'required|date_format:H:i',
            'duration_minutes'  => 'required|integer|in:15,30,45,60',
            'consultation_type' => 'required|string|in:chat,call,video,in_person',
            'notes'             => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'booking_date.after_or_equal' => 'Please select today or a future date for your consultation.',
            'start_time.required'        => 'Please choose an available time slot.',
            'start_time.date_format'     => 'The selected time format is invalid.',
            'duration_minutes.in'        => 'Consultation duration must be 15, 30, 45, or 60 minutes.',
            'consultation_type.in'       => 'Please select a valid consultation mode (Chat, Voice Call, or Video Call).',
        ];
    }
}
