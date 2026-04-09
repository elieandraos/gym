<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CancelBookingSlot;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
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
            'booking' => BookingResource::make($bookingSlot->booking),
        ]);
    }

    public function destroy(BookingSlot $bookingSlot, CancelBookingSlot $cancelBookingSlot): RedirectResponse
    {
        $cancelBookingSlot->handle($bookingSlot);

        return redirect()->route('admin.bookings-slots.show', [
            'bookingSlot' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking_id,
        ]);
    }
}
