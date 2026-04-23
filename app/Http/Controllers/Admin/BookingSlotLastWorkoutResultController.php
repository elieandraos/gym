<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuitWorkoutSet;
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

        $sets = null;
        $slotDate = null;

        if ($previousSlot) {
            $circuitWorkout = $previousSlot->circuits()
                ->with(['circuitWorkouts' => fn ($q) => $q->where('workout_id', $workoutId)->with('sets')])
                ->get()
                ->flatMap(fn ($c) => $c->circuitWorkouts)
                ->first();

            $sets = $circuitWorkout?->sets ?? null;
            $slotDate = $previousSlot->start_time->format('M j, Y');
        }

        $base = BookingSlotCircuitWorkoutSet::query()
            ->whereHas('circuitWorkout', fn ($q) => $q->where('workout_id', $workoutId))
            ->whereHas('circuitWorkout.circuit.bookingSlot', fn ($q) => $q
                ->whereHas('booking', fn ($q2) => $q2->where('member_id', $memberId))
                ->where('id', '!=', $bookingSlot->id)
                ->where('start_time', '>', now()->subYear())
                ->where('start_time', '<', $bookingSlot->start_time)
            );

        $bestWeight = (clone $base)->whereNotNull('weight_in_kg')->orderByDesc('weight_in_kg')->first();
        $bestDuration = (clone $base)->whereNotNull('duration_in_seconds')->orderByDesc('duration_in_seconds')->first();
        $personalBest = $bestWeight ?? $bestDuration;

        return response()->json([
            'sets' => $sets,
            'slot_date' => $slotDate,
            'personal_best' => $personalBest?->only(['reps', 'weight_in_kg', 'duration_in_seconds']),
        ]);
    }
}
