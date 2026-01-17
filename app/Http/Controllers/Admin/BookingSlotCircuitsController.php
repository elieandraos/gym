<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingSlotCircuitsController extends Controller
{
    public function store(Request $request, BookingSlot $bookingSlot): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        // Auto-generate name if not provided
        $circuitCount = $bookingSlot->circuits()->count();
        $name = $validated['name'] ?? 'Circuit '.($circuitCount + 1);

        $bookingSlot->circuits()->create([
            'name' => $name,
        ]);

        return redirect()->back()
            ->with('flash.banner', 'Circuit created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function update(Request $request, BookingSlot $bookingSlot, BookingSlotCircuit $circuit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $circuit->update($validated);

        return redirect()->back()
            ->with('flash.banner', 'Circuit updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function destroy(BookingSlot $bookingSlot, BookingSlotCircuit $circuit): RedirectResponse
    {
        $circuit->delete();

        return redirect()->back()
            ->with('flash.banner', 'Circuit deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
