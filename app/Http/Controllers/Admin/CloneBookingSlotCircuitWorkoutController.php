<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CloneBookingSlotCircuitWorkout;
use App\Actions\Admin\CreateBookingSlotCircuit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CloneBookingSlotCircuitWorkoutRequest;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Http\RedirectResponse;

class CloneBookingSlotCircuitWorkoutController extends Controller
{
    public function __invoke(
        CloneBookingSlotCircuitWorkoutRequest $request,
        BookingSlot $bookingSlot,
        CloneBookingSlotCircuitWorkout $cloneBookingSlotCircuitWorkout,
        CreateBookingSlotCircuit $createBookingSlotCircuit,
    ): RedirectResponse {
        $sourceWorkout = BookingSlotCircuitWorkout::with('sets')->findOrFail($request->integer('source_workout_id'));

        if ($request->filled('circuit_id')) {
            $targetCircuit = BookingSlotCircuit::query()->findOrFail($request->integer('circuit_id'));
        } else {
            $createBookingSlotCircuit->handle($bookingSlot, []);
            $targetCircuit = $bookingSlot->circuits()->latest()->first();
        }

        $cloneBookingSlotCircuitWorkout->handle($sourceWorkout, $targetCircuit);

        return redirect()->back()
            ->with('flash.banner', 'Workout cloned successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
