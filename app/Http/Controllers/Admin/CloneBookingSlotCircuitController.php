<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CloneBookingSlotCircuit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CloneBookingSlotCircuitRequest;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use Illuminate\Http\RedirectResponse;

class CloneBookingSlotCircuitController extends Controller
{
    public function __invoke(CloneBookingSlotCircuitRequest $request, BookingSlot $bookingSlot, CloneBookingSlotCircuit $cloneBookingSlotCircuit): RedirectResponse
    {
        $sourceCircuit = BookingSlotCircuit::query()->findOrFail($request->integer('source_circuit_id'));

        $cloneBookingSlotCircuit->handle($sourceCircuit, $bookingSlot);

        return redirect()->back()
            ->with('flash.banner', 'Circuit cloned successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
