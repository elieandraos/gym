<?php

namespace App\Http\Requests\Admin;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\LeadSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:6144'],
            'remove_photo' => ['nullable', 'boolean'],
            'registration_date' => ['required', 'date'],
            'lead_source' => ['nullable', new Enum(LeadSource::class)],
            'gender' => ['required', new Enum(Gender::class)],
            'weight' => ['required', 'integer', 'min:50', 'max:150'],
            'height' => ['required', 'integer', 'min:150', 'max:210'],
            'birthdate' => ['required', 'date'],
            'blood_type' => ['required', new Enum(BloodType::class)],
            'phone_number' => ['required', 'string'],
            'instagram_handle' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
            'color' => ['required', 'string'],
        ];
    }
}
