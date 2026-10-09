<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate this at the route/middleware level for your admin auth
    }

    public function rules(): array
    {
        // "sometimes" — this is a partial-update endpoint (PATCH/PUT), every field is optional.
        return [
            'customer_name'  => ['sometimes', 'string', 'max:150'],
            'customer_email' => ['sometimes', 'email', 'max:190'],
            'customer_phone' => ['sometimes', 'string', 'max:20'],
            'booking_date'   => ['sometimes', 'date'],
            'booking_time'   => ['sometimes', 'date_format:H:i'],
            'party_size'     => ['sometimes', 'integer', 'min:1', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'status'         => ['sometimes', Rule::in(Booking::STATUSES)],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error'   => [
                'code'    => 'VALIDATION_FAILED',
                'message' => $validator->errors()->first(),
            ],
        ], 422));
    }
}
