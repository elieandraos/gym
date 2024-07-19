<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Inertia\Inertia;

class BookingSlotsController extends Controller
{
    public function show(BookingSlot $bookingSlot)
    {
        $bookingSlot->load(['booking', 'booking.member', 'booking.trainer']);

        return Inertia::render('Admin/BookingsSlots/Show', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot)
        ]);
    }
}
