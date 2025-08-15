<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeBookingSlotDateTimeRequest;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotsController extends Controller
{
    public function show(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking',
            'booking.member',
            'booking.trainer',
            'bookingSlotWorkouts.workout',
            'bookingSlotWorkouts.sets',
        ]);

        return Inertia::render('Admin/BookingsSlots/Show', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }
}
