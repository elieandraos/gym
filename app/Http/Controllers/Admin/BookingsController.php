<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingsController extends Controller
{

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }


    public function show(Booking $booking) : Response
    {
        $booking->load(['member', 'trainer', 'bookingSlots' => function ($query) {
            $query->orderBy('start_time');
        }]);

        return Inertia::render('Admin/Booking/Show', [
            'booking' => BookingResource::make($booking)
        ]);
    }

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
