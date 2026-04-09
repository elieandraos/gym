<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateBookingSlotCircuitWorkout;
use App\Actions\Admin\DeleteBookingSlotCircuitWorkout;
use App\Actions\Admin\UpdateBookingSlotCircuitWorkout;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingSlotCircuitWorkoutRequest;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Http\RedirectResponse;

class BookingSlotCircuitWorkoutsController extends Controller
{
    /** @noinspection PhpUnusedParameterInspection */
    public function store(BookingSlotCircuitWorkoutRequest $request, BookingSlot $bookingSlot, BookingSlotCircuit $circuit, CreateBookingSlotCircuitWorkout $createBookingSlotCircuitWorkout): RedirectResponse
    {
        $createBookingSlotCircuitWorkout->handle($circuit, $request->validated());

        return redirect()->back()
            ->with('flash.banner', 'Workout added successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function update(BookingSlotCircuitWorkoutRequest $request, BookingSlot $bookingSlot, BookingSlotCircuit $circuit, BookingSlotCircuitWorkout $circuitWorkout, UpdateBookingSlotCircuitWorkout $updateBookingSlotCircuitWorkout): RedirectResponse
    {
        $updateBookingSlotCircuitWorkout->handle($circuitWorkout, $request->validated());

        return redirect()->back()
            ->with('flash.banner', 'Workout updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function destroy(BookingSlot $bookingSlot, BookingSlotCircuit $circuit, BookingSlotCircuitWorkout $circuitWorkout, DeleteBookingSlotCircuitWorkout $deleteBookingSlotCircuitWorkout): RedirectResponse
    {
        $deleteBookingSlotCircuitWorkout->handle($circuitWorkout);

        return redirect()->back()
            ->with('flash.banner', 'Workout deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
