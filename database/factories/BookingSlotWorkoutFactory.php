<?php

namespace Database\Factories;

use App\Models\BookingSlot;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingSlotWorkout>
 */
class BookingSlotWorkoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_slot_id' => BookingSlot::factory(),
            'workout_id' => Workout::factory(),
        ];
    }
}
