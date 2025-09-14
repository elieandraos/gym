<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingSlotWorkoutRequest;
use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
use App\Models\BookingSlotWorkout;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotWorkoutController extends Controller
{
    public function create(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking',
            'booking.member',
            'booking.trainer',
        ]);

        $workouts = Workout::query()->orderBy('category')->orderBy('name')->get();

        return Inertia::render('Admin/BookingSlotWorkout/Create', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
            'workouts' => WorkoutResource::collection($workouts),
        ]);
    }

    public function store(BookingSlotWorkoutRequest $request, BookingSlot $bookingSlot): RedirectResponse
    {
        foreach ($request->input('workouts', []) as $workoutData) {
            $bookingSlotWorkout = BookingSlotWorkout::query()->create([
                'booking_slot_id' => $bookingSlot->id,
                'workout_id' => $workoutData['id'],
            ]);

            $sets = [];
            $type = $workoutData['type'];
            $weights = $workoutData['weight_in_kg'] ?? [];
            $durations = $workoutData['duration_in_seconds'] ?? [];
            $reps = $workoutData['reps'] ?? [];

            for ($i = 0; $i < max(count($weights), count($durations)); $i++) {
                $sets[] = [
                    'reps' => $reps[$i] ?? 12,
                    'is_timed' => $type === 'seconds',
                    'is_weighted' => $type === 'weight',
                    'weight_in_kg' => $type === 'weight' ? ($weights[$i] !== '' ? $weights[$i] : null) : null,
                    'duration_in_seconds' => $type === 'seconds' ? ($durations[$i] !== '' ? $durations[$i] : null) : null,
                ];
            }

            $bookingSlotWorkout->sets()->createMany($sets);
        }

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }

    public function destroy(BookingSlotWorkout $bookingSlotWorkout): RedirectResponse
    {
        $bookingSlotId = $bookingSlotWorkout->booking_slot_id;
        $bookingSlotWorkout->delete();

        return redirect()->route('admin.bookings-slots.show', $bookingSlotId);
    }
}
