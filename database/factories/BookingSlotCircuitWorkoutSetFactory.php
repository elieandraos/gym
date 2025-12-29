<?php

namespace Database\Factories;

use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingSlotCircuitWorkoutSet>
 */
class BookingSlotCircuitWorkoutSetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_slot_circuit_workout_id' => BookingSlotCircuitWorkout::factory(),
            'reps' => 12,
            'weight_in_kg' => null,
            'duration_in_seconds' => null,
        ];
    }

    public function weighted(): BookingSlotCircuitWorkoutSetFactory
    {
        return $this->state(function () {
            return [
                'reps' => $this->faker->numberBetween(8, 15),
                'weight_in_kg' => $this->faker->randomFloat(2, 2.5, 35),
                'duration_in_seconds' => null,
            ];
        });
    }

    public function timed(): BookingSlotCircuitWorkoutSetFactory
    {
        return $this->state(function () {
            return [
                'reps' => 1,
                'weight_in_kg' => null,
                'duration_in_seconds' => $this->faker->numberBetween(30, 180),
            ];
        });
    }
}
