<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UnfreezeBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slots' => ['required', 'array'],
            'slots.*.id' => ['required', 'integer', 'exists:booking_slots,id'],
            'slots.*.start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'slots.*.end_time' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
