<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\UnfreezeBooking;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnfreezeBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingSlotResource;
use App\Models\Booking;
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

    public function update(UnfreezeBookingRequest $request, Booking $booking, UnfreezeBooking $unfreezeBooking): RedirectResponse
    {
        $unfreezeBooking->handle($booking, $request->validated('slots'));

        return redirect()->route('admin.members.show', $booking->member_id)
            ->with('flash.banner', 'Booking unfrozen successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
