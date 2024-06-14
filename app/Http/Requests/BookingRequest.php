<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nb_sessions' => ['required', 'integer'],
            'member_id' => ['required', 'exists:users,id'],
            'trainer_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
