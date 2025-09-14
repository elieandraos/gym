<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeBookingSlotDateTimeRequest;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChangeBookingSlotDateTimeController extends Controller
{
    public function edit(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking',
            'booking.member',
            'booking.trainer',
        ]);

        return Inertia::render('Admin/ChangeBookingSlotDateTime/Edit', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }

    public function update(ChangeBookingSlotDateTimeRequest $request, BookingSlot $bookingSlot): RedirectResponse
    {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $request->validated('start_time'), 'Asia/Beirut');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $request->validated('end_time'), 'Asia/Beirut');

        $bookingSlot->update([
            'start_time' => $start,
            'end_time' => $end,
            'status' => $start->isPast() ? Status::Complete : Status::Upcoming,
        ]);

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }
}
