<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\MarkBookingAsPaid;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class MarkBookingAsPaidController extends Controller
{
    public function __invoke(Booking $booking, MarkBookingAsPaid $markBookingAsPaid): RedirectResponse
    {
        $markBookingAsPaid->handle($booking);

        return redirect()->back()->with('success', "{$booking->member->first_name}'s training has been marked as paid.");
    }
}
