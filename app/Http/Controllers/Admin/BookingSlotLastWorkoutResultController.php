<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingSlotLastWorkoutResultController
{
    public function __invoke(Request $request, BookingSlot $bookingSlot): JsonResponse
    {
        $workoutId = $request->integer('workout_id');
        $memberId = $bookingSlot->booking->member_id;

        $previousSlot = BookingSlot::query()
            ->whereHas('booking', fn ($q) => $q->where('member_id', $memberId))
            ->where('id', '!=', $bookingSlot->id)
            ->where('start_time', '<', $bookingSlot->start_time)
            ->whereHas('circuits.circuitWorkouts', fn ($q) => $q->where('workout_id', $workoutId))
            ->orderByDesc('start_time')
            ->first();

        if (! $previousSlot) {
            return response()->json(['sets' => null]);
        }

        $circuitWorkout = $previousSlot->circuits()
            ->with(['circuitWorkouts' => fn ($q) => $q->where('workout_id', $workoutId)->with('sets')])
            ->get()
            ->flatMap(fn ($c) => $c->circuitWorkouts)
            ->first();

        return response()->json(['sets' => $circuitWorkout?->sets ?? null]);
    }
}
