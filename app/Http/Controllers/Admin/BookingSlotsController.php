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
    public function show(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking',
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
            'booking' => array_merge(
                BookingResource::make($bookingSlot->booking)->resolve(),
                [
                    'member' => [
                        'id' => $bookingSlot->booking->member->id,
                        'name' => $bookingSlot->booking->member->name,
                        'profile_photo_url' => $bookingSlot->booking->member->profile_photo_url,
                    ],
                    'trainer' => [
                        'id' => $bookingSlot->booking->trainer->id,
                        'name' => $bookingSlot->booking->trainer->name,
                        'profile_photo_url' => $bookingSlot->booking->trainer->profile_photo_url,
                    ],
                ]
            ),
            'bookingId' => request('booking_id'),
            'workouts' => WorkoutResource::collection($workouts),
        ]);
    }
}
