<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\FreezeBooking;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FreezeBookingController extends Controller
{
    public function index(Booking $booking): Response
    {
        $booking->load([
            'member.memberActiveBooking',
            'trainer',
        ]);

        return Inertia::render('Admin/FreezeBooking/Index', [
            'booking' => BookingResource::make($booking),
        ]);
    }

    public function update(Booking $booking, FreezeBooking $freezeBooking): RedirectResponse
    {
        if ($booking->is_frozen) {
            return back()
                ->with('flash.banner', 'This booking is already frozen')
                ->with('flash.bannerStyle', 'danger');
        }

        $freezeBooking->handle($booking);

        return redirect()->route('admin.members.show', $booking->member_id)
            ->with('flash.banner', 'Booking frozen successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
