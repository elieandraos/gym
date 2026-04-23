<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CloneBookingSlotCircuitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_circuit_id' => ['required', 'integer', 'exists:booking_slot_circuits,id'],
        ];
    }
}
