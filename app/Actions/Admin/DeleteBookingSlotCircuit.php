<?php

namespace App\Actions\Admin;

use App\Models\BookingSlotCircuit;

class DeleteBookingSlotCircuit
{
    public function handle(BookingSlotCircuit $circuit): void
    {
        $circuit->delete();
    }
}
