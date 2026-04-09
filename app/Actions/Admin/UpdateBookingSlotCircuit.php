<?php

namespace App\Actions\Admin;

use App\Models\BookingSlotCircuit;

class UpdateBookingSlotCircuit
{
    public function handle(BookingSlotCircuit $circuit, array $attributes): void
    {
        $circuit->update($attributes);
    }
}
