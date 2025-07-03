<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
use App\Models\Workout;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotWorkoutController extends Controller
{
    public function create(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load(['booking', 'booking.member', 'booking.trainer']);

        $workouts = WorkoutResource::collection(
            Workout::query()->orderBy('category')->orderBy('name')->get()
        );

        return Inertia::render('Admin/BookingSlotWorkout/Create', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
            'workouts' => $workouts,
        ]);
    }
}
