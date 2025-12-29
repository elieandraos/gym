<?php

namespace Database\Factories;

use App\Models\BookingSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingSlotCircuitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_slot_id' => BookingSlot::factory(),
            'name' => null,
        ];
    }

    public function named(string $name): BookingSlotCircuitFactory
    {
        return $this->state(fn (array $attributes) => ['name' => $name]);
    }
}
