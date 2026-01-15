<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnfreezeBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingSlotResource;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UnfreezeBookingController extends Controller
{
    public function index(Booking $booking): Response
    {
        $booking->load([
            'member.memberActiveBooking',
            'trainer',
            'bookingSlots' => function ($query) {
                $query->where('status', Status::Frozen)
                    ->orderBy('start_time');
            },
        ]);

        return Inertia::render('Admin/UnfreezeBooking/Index', [
            'booking' => BookingResource::make($booking),
            'frozenSlots' => BookingSlotResource::collection($booking->bookingSlots),
        ]);
    }

    public function update(UnfreezeBookingRequest $request, Booking $booking): RedirectResponse
    {
        $slots = $request->validated('slots');

        foreach ($slots as $slotData) {
            $bookingSlot = $booking->bookingSlots()->findOrFail($slotData['id']);

            $start = Carbon::createFromFormat('Y-m-d H:i:s', $slotData['start_time'], 'Asia/Beirut');
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $slotData['end_time'], 'Asia/Beirut');

            $bookingSlot->update([
                'start_time' => $start,
                'end_time' => $end,
                'status' => $start->isPast() ? Status::Complete : Status::Upcoming,
            ]);
        }

        $booking->update([
            'is_frozen' => false,
            'frozen_at' => null,
        ]);

        // Update booking end_date to the last slot's date
        $booking->updateEndDateToLastSlot();

        return redirect()->route('admin.members.show', $booking->member_id)
            ->with('flash.banner', 'Booking unfrozen successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
