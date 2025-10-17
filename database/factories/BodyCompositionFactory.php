<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BodyComposition>
 */
class BodyCompositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'photo_path' => 'body-compositions/'.$this->faker->uuid().'.jpg',
            'taken_at' => $this->faker->dateTimeBetween('-6 months'),
        ];
    }
}
