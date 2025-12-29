<?php

namespace Database\Factories;

use App\Models\BookingSlotCircuit;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingSlotCircuitWorkout>
 */
class BookingSlotCircuitWorkoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_slot_circuit_id' => BookingSlotCircuit::factory(),
            'workout_id' => Workout::factory(),
        ];
    }
}
