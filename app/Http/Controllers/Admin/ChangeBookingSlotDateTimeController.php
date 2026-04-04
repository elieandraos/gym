<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ChangeBookingSlotDateTime;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeBookingSlotDateTimeRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use App\Services\BookingManager;
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

        // Calculate next available date and time based on booking schedule
        $suggestedDate = null;
        $suggestedTime = null;
        if ($bookingSlot->booking->schedule_days && $bookingSlot->booking->end_date) {
            $suggestion = BookingManager::getNextAvailableDateTime(
                Carbon::parse($bookingSlot->booking->end_date),
                $bookingSlot->booking->schedule_days
            );

            if ($suggestion) {
                $suggestedDate = $suggestion['date'];
                $suggestedTime = $suggestion['time'];
            }
        }

        return Inertia::render('Admin/ChangeBookingSlotDateTime/Edit', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
            'booking' => BookingResource::make($bookingSlot->booking),
            'suggestedDate' => $suggestedDate,
            'suggestedTime' => $suggestedTime,
        ]);
    }

    public function update(ChangeBookingSlotDateTimeRequest $request, BookingSlot $bookingSlot, ChangeBookingSlotDateTime $changeBookingSlotDateTime): RedirectResponse
    {
        $changeBookingSlotDateTime->handle($bookingSlot, $request->validated());

        return redirect()->route('admin.bookings-slots.show', [
            'bookingSlot' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking_id,
        ]);
    }
}
