<?php

namespace App\Actions\Fortify;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'registration_date' => ['required', 'date'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'weight' => ['nullable', 'integer'],
            'height' => ['nullable', 'integer'],
            'birthdate' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(Gender::cases())],
            'blood_type' => ['nullable', Rule::in(BloodType::cases())],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'registration_date' => $input['registration_date'],
            'in_house' => $input['in_house'] ?? null,
            'gender' => $input['gender'] ?? null,
            'weight' => $input['weight'] ?? null,
            'height' => $input['height'] ?? null,
            'birthdate' => $input['birthdate'] ?? null,
            'blood_type' => $input['blood_type'] ?? null,
            'phone_number' => $input['phone_number'] ?? null,
            'instagram_handle' => $input['instagram_handle'] ?? null,
            'address' => $input['address'] ?? null,
            'emergency_contact' => $input['emergency_contact'] ?? null,
        ])->save();
    }
}
