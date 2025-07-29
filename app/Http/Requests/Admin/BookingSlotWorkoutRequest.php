<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingSlotWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workouts' => ['nullable', 'array'],
            'workouts.*.id' => ['required', 'exists:workouts,id'],
            'workouts.*.type' => ['required', Rule::in(['weight', 'seconds'])],
            'workouts.*.weight_in_kg' => ['array'],
            'workouts.*.reps' => ['array'],
            'workouts.*.duration_in_seconds' => ['array'],
        ];
    }
}
