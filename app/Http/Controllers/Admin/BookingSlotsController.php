<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
use App\Models\Workout;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotsController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function show(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking.member',
            'booking.trainer',
            'circuits' => fn ($query) => $query->orderBy('created_at'),
            'circuits.circuitWorkouts.workout',
            'circuits.circuitWorkouts.sets' => fn ($query) => $query->orderBy('id'),
        ]);

        $workouts = Workout::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/BookingsSlots/Show', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
            'booking' => BookingResource::make($bookingSlot->booking),
            'bookingId' => request('booking_id'),
            'workouts' => WorkoutResource::collection($workouts),
        ]);
    }
}
