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
    public function show(BookingSlot $bookingSlot): \Illuminate\Http\JsonResponse
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

        // DEBUG: Return raw JSON to test serialization
        $bookingData = BookingResource::make($bookingSlot->booking)->toArray(request());
        $result = @json_encode($bookingData);
        dd('json error', [
            'result' => $result,
            'error_code' => json_last_error(),
            'error_msg' => json_last_error_msg(),
        ]);
        return response()->json([
            'success' => true,
            'booking' => BookingResource::make($bookingSlot->booking)->toArray(request()),
        ]);

//        return Inertia::render('Admin/BookingsSlots/Show', [
//            'bookingSlot' => BookingSlotResource::make($bookingSlot), // Now includes nested circuits with workouts
//            'booking' => BookingResource::make($bookingSlot->booking), // Now includes nested member, trainer
//            'bookingId' => request('booking_id'),
//            'workouts' => WorkoutResource::collection($workouts),
//        ]);
    }
}
