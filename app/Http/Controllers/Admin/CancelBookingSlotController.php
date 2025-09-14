<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CancelBookingSlotController extends Controller
{
    public function index(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking',
            'booking.member',
            'booking.trainer',
        ]);

        return Inertia::render('Admin/CancelBookingSlot/Index', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
        ]);
    }

    public function destroy(BookingSlot $bookingSlot): RedirectResponse
    {
        $bookingSlot->update([
            'status' => Status::Cancelled,
        ]);

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }
}
