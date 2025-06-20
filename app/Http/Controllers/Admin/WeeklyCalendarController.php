<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarWeekCollection;
use App\Models\Booking;
use App\Services\BookingManager;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Inertia\Inertia;
use Inertia\Response;

class WeeklyCalendarController extends Controller
{
    public function index(): Response
    {
        ['start' => $spanStart, 'end' => $spanEnd] = BookingManager::getCalendarSpan();

        $bookings = Booking::with([
                'bookingSlots' => fn($q) => $q->between($spanStart, $spanEnd),
                'member:id,name',
                'trainer:id,name',
            ])
            ->between($spanStart, $spanEnd)
            ->get();

        return Inertia::render('Admin/Calendar/Index', [
            'weeks' => new CalendarWeekCollection($bookings)
        ]);
    }
}
