<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteBooking;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MemberResource;
use App\Models\Booking;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DeleteBookingController extends Controller
{
    public function index(Booking $booking): Response
    {
        $booking->load(['member', 'trainer']);

        $completedSessions = $booking->bookingSlots()->where('status', Status::Complete)->count();
        $totalCircuits = BookingSlotCircuit::query()->whereHas('bookingSlot', fn ($q) => $q->where('booking_id', $booking->id))->count();
        $totalExercises = BookingSlotCircuitWorkout::query()->whereHas('circuit.bookingSlot', fn ($q) => $q->where('booking_id', $booking->id))->count();

        $bookingData = array_merge(
            BookingResource::make($booking)->resolve(),
            [
                'completed_sessions' => $completedSessions,
                'total_circuits' => $totalCircuits,
                'total_exercises' => $totalExercises,
            ]
        );

        return Inertia::render('Admin/DeleteTraining/Index', [
            'booking' => $bookingData,
            'member' => MemberResource::make($booking->member),
        ]);
    }

    public function destroy(Booking $booking, DeleteBooking $deleteBooking): RedirectResponse
    {
        $memberId = $booking->member_id;

        $deleteBooking->handle($booking);

        return redirect()->route('admin.members.show', $memberId)
            ->with('flash.banner', 'Training removed successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
