<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'billiard_table_id' => ['required', 'exists:billiard_tables,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'customer_name' => ['required', 'string', 'min:2', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{8,15}$/'],
            'booking_type' => ['required', 'in:online'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'customer_notes' => ['nullable', 'string'],
        ];
    }
}
