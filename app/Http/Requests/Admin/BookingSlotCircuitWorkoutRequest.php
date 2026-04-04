<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BookingSlotCircuitWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workout_id' => ['required', 'exists:workouts,id'],
            'type' => ['required', 'in:weight,duration'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'sets' => ['required', 'array', 'min:1', 'max:10'],
            'sets.*.reps' => ['nullable', 'integer', 'min:1', 'max:999'],
            'sets.*.weight_in_kg' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'sets.*.duration_in_seconds' => ['nullable', 'integer', 'min:1', 'max:7200'],
        ];
    }
}
