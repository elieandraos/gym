<?php

namespace App\Actions\Admin;

use App\Models\BookingSlot;

class CreateBookingSlotCircuit
{
    public function handle(BookingSlot $bookingSlot, array $attributes): void
    {
        $circuitCount = $bookingSlot->circuits()->count();
        $name = $attributes['name'] ?? 'Circuit '.($circuitCount + 1);

        $bookingSlot->circuits()->create(['name' => $name]);
    }
}
