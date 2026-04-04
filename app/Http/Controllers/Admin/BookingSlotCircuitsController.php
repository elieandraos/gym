<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateBookingSlotCircuit;
use App\Actions\Admin\DeleteBookingSlotCircuit;
use App\Actions\Admin\UpdateBookingSlotCircuit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingSlotCircuitRequest;
use App\Http\Requests\Admin\UpdateBookingSlotCircuitRequest;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use Illuminate\Http\RedirectResponse;

class BookingSlotCircuitsController extends Controller
{
    public function store(BookingSlotCircuitRequest $request, BookingSlot $bookingSlot, CreateBookingSlotCircuit $createBookingSlotCircuit): RedirectResponse
    {
        $createBookingSlotCircuit->handle($bookingSlot, $request->validated());

        return redirect()->back()
            ->with('flash.banner', 'Circuit created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function update(UpdateBookingSlotCircuitRequest $request, BookingSlot $bookingSlot, BookingSlotCircuit $circuit, UpdateBookingSlotCircuit $updateBookingSlotCircuit): RedirectResponse
    {
        $updateBookingSlotCircuit->handle($circuit, $request->validated());

        return redirect()->back()
            ->with('flash.banner', 'Circuit updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function destroy(BookingSlot $bookingSlot, BookingSlotCircuit $circuit, DeleteBookingSlotCircuit $deleteBookingSlotCircuit): RedirectResponse
    {
        $deleteBookingSlotCircuit->handle($circuit);

        return redirect()->back()
            ->with('flash.banner', 'Circuit deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
