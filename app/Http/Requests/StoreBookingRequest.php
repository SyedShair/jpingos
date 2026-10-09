<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // booking creation is a public storefront action
    }

    public function rules(): array
    {
        return [
            'event_slug'     => ['nullable', 'string', 'exists:booking_events,slug'],
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'booking_date'   => ['required', 'date', 'after_or_equal:today'],
            'booking_time'   => ['required', 'date_format:H:i'],
            'party_size'     => ['required', 'integer', 'min:1', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            // Accepted from the body; the controller also accepts it as an Idempotency-Key header.
            'idempotency_key' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** Same {success:false,error:{code,message}} envelope the rest of the API uses. */
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
