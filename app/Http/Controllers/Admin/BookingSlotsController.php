<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotsController extends Controller
{
    public function show(BookingSlot $bookingSlot) : Response
    {
        $bookingSlot->load(['booking', 'booking.member', 'booking.trainer']);

        return Inertia::render('Admin/BookingsSlots/Show', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }

    public function edit(BookingSlot $bookingSlot) : Response
    {
        $bookingSlot->load('booking');

        return Inertia::render('Admin/BookingsSlots/Edit', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }

    public function update(Request $request, BookingSlot $bookingSlot) : RedirectResponse
    {
        $startTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $request->get('start_time'), 'UTC')
            ->setTimezone('Asia/Beirut');

        $endTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $request->get('end_time'), 'UTC')
            ->setTimezone('Asia/Beirut');

        $bookingSlot->update(['start_time' => $startTime, 'end_time' => $endTime ]);

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }
}
