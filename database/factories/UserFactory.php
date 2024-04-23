<?php

namespace Database\Factories;

use App\Enums\BloodType;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(Gender::cases());

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,

            'registration_date' => fake()->dateTimeBetween('-2 years'),
            'in_house' => fake()->boolean(),
            'gender' => $gender,
            'weight' => fake()->numberBetween(50, 120),
            'height' => fake()->numberBetween(150, 210),
            'birthdate' => fake()->dateTimeBetween('-50 years', '-20 years'),
            'blood_type' => fake()->randomElement(BloodType::cases()),
            'phone_number' => fake()->phoneNumber(),
            'instagram_handle' => fake()->userName(),
            'address' => fake()->address(),
            'emergency_contact' => fake()->name($gender),
        ];
    }
}
