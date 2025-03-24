<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingSlotRequest;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotsController extends Controller
{
    public function show(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load(['booking', 'booking.member', 'booking.trainer']);

        return Inertia::render('Admin/BookingsSlots/Show', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }

    public function edit(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load('booking');

        return Inertia::render('Admin/BookingsSlots/Edit', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }

    public function update(UpdateBookingSlotRequest $request, BookingSlot $bookingSlot): RedirectResponse
    {
        $bookingSlot->load('booking', 'booking.bookingSlots');

        $bookingSlot->update([
            'start_time' => Carbon::createFromFormat('Y-m-d H:i:s', $request->validated('start_time'), 'Asia/Beirut'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i:s', $request->validated('end_time'), 'Asia/Beirut'),
        ]);

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }
}
