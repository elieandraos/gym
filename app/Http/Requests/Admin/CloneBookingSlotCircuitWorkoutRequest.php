<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CloneBookingSlotCircuitWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_workout_id' => ['required', 'integer', 'exists:booking_slot_circuit_workouts,id'],
            'circuit_id' => ['nullable', 'integer', 'exists:booking_slot_circuits,id'],
        ];
    }
}
