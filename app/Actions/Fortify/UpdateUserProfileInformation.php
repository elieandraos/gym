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
            'name' => $input['name'] ?? $user->name,
            'email' => $input['email'] ?? $user->email,
            'registration_date' => $input['registration_date'] ?? $user->registration_date,
            'in_house' => $input['in_house'] ?? $user->in_house,
            'gender' => $input['gender'] ?? $user->gender,
            'weight' => $input['weight'] ?? $user->weight,
            'height' => $input['height'] ?? $user->height,
            'birthdate' => $input['birthdate'] ?? $user->birthdate,
            'blood_type' => $input['blood_type'] ?? $user->blood_type,
            'phone_number' => $input['phone_number'] ?? $user->phone_number,
            'instagram_handle' => $input['instagram_handle'] ?? $user->instagram_handle,
            'address' => $input['address'] ?? $user->address,
            'emergency_contact' => $input['emergency_contact'] ?? $user->emergency_contact,
        ])->save();
    }
}
