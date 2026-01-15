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

        dd('Controller completed', [
            'slot_id' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking->id,
            'circuits_count' => $bookingSlot->circuits->count(),
            'workouts_count' => $workouts->count(),
        ]);
        
        return Inertia::render('Admin/BookingsSlots/Show', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot), // Now includes nested circuits with workouts
            'booking' => BookingResource::make($bookingSlot->booking), // Now includes nested member, trainer
            'bookingId' => request('booking_id'),
            'workouts' => WorkoutResource::collection($workouts),
        ]);
    }
}
