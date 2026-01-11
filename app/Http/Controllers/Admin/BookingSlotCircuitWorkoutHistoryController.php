<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingSlotCircuitWorkoutHistoryController extends Controller
{
    /**
     * Get previous sessions with circuits and workouts for a booking slot.
     */
    public function __invoke(Request $request, BookingSlot $bookingSlot): JsonResponse
    {
        $limit = $request->integer('limit', 2);

        // Get the previous sessions for the same booking
        $previousSessions = BookingSlot::query()
            ->where('booking_id', $bookingSlot->booking_id)
            ->where('id', '!=', $bookingSlot->id)
            ->where('start_time', '<', $bookingSlot->start_time)
            ->with([
                'circuits' => fn ($query) => $query->orderBy('created_at'),
                'circuits.circuitWorkouts.workout',
                'circuits.circuitWorkouts.sets' => fn ($query) => $query->orderBy('id'),
            ])
            ->orderBy('start_time', 'desc')
            ->limit($limit)
            ->get();

        // Unset the booking relationship on each slot to prevent circular serialization
        $previousSessions->each(function ($slot) {
            $slot->unsetRelation('booking');
        });

        return response()->json([
            'previousSessions' => BookingSlotResource::collection($previousSessions),
        ]);
    }
}
