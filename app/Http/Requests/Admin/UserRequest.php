<?php

namespace App\Http\Requests\Admin;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'registration_date' => ['required', 'date'],
            'in_house' => ['required', 'boolean'],
            'gender' => ['required', new Enum(Gender::class)],
            'weight' => ['required', 'integer', 'min:50', 'max:150'],
            'height' => ['required', 'integer', 'min:150', 'max:210'],
            'birthdate' => ['required', 'date'],
            'blood_type' => ['required', new Enum(BloodType::class)],
            'phone_number' => ['required', 'string'],
            'instagram_handle' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string']
        ];
    }
}
