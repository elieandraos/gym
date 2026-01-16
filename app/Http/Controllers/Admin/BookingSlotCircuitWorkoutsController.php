<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingSlotCircuitWorkoutsController extends Controller
{
    /** @noinspection PhpUnusedParameterInspection */
    public function store(Request $request, BookingSlot $bookingSlot, BookingSlotCircuit $circuit): RedirectResponse
    {
        $validated = $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'type' => 'required|in:weight,duration',
            'sets' => 'required|array|min:1|max:10',
            'sets.*.reps' => 'nullable|integer|min:1|max:999',
            'sets.*.weight_in_kg' => 'nullable|numeric|min:0|max:999',
            'sets.*.duration_in_seconds' => 'nullable|integer|min:1|max:7200',
        ]);

        /** @var BookingSlotCircuitWorkout $circuitWorkout */
        $circuitWorkout = $circuit->circuitWorkouts()->create([
            'workout_id' => $validated['workout_id'],
        ]);

        // Create all sets
        foreach ($validated['sets'] as $setData) {
            $circuitWorkout->sets()->create([
                'reps' => $setData['reps'] ?? null,
                'weight_in_kg' => $setData['weight_in_kg'] ?? null,
                'duration_in_seconds' => $setData['duration_in_seconds'] ?? null,
            ]);
        }

        return redirect()->back()
            ->with('flash.banner', 'Workout added successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function update(Request $request, BookingSlot $bookingSlot, BookingSlotCircuit $circuit, BookingSlotCircuitWorkout $circuitWorkout): RedirectResponse
    {
        $validated = $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'type' => 'required|in:weight,duration',
            'sets' => 'required|array|min:1|max:10',
            'sets.*.reps' => 'nullable|integer|min:1|max:999',
            'sets.*.weight_in_kg' => 'nullable|numeric|min:0|max:999',
            'sets.*.duration_in_seconds' => 'nullable|integer|min:1|max:7200',
        ]);

        // Update the workout
        $circuitWorkout->update([
            'workout_id' => $validated['workout_id'],
        ]);

        // Delete existing sets and create new ones
        $circuitWorkout->sets()->delete();

        // Create new sets
        foreach ($validated['sets'] as $setData) {
            $circuitWorkout->sets()->create([
                'reps' => $setData['reps'] ?? null,
                'weight_in_kg' => $setData['weight_in_kg'] ?? null,
                'duration_in_seconds' => $setData['duration_in_seconds'] ?? null,
            ]);
        }

        return redirect()->back()
            ->with('flash.banner', 'Workout updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function destroy(BookingSlot $bookingSlot, BookingSlotCircuit $circuit, BookingSlotCircuitWorkout $circuitWorkout): RedirectResponse
    {
        $circuitWorkout->delete();

        return redirect()->back()
            ->with('flash.banner', 'Workout deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
