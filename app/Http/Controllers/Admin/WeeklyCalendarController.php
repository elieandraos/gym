<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarWeekCollection;
use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Inertia\Inertia;
use Inertia\Response;

class WeeklyCalendarController extends Controller
{
    public function index() : Response
    {
        $today      = Carbon::today();
        $monday     = $today->copy()->startOfWeek(CarbonInterface::MONDAY);
        $saturday   = $monday->copy()->addDays(5);

        // 7-week span around Monday→Saturday
        $spanStart = $monday->copy()->subWeeks(3);
        $spanEnd   = $saturday->copy()->addWeeks(3);

        $bookings = Booking::with([
            // Only load slots that at least partially overlap our span
            'bookingSlots' => function($q) use ($spanStart, $spanEnd) {
                $q->where(function($q) use ($spanStart, $spanEnd) {
                    $q->whereBetween('start_time', [
                        $spanStart->startOfDay(),
                        $spanEnd->endOfDay(),
                    ])
                        ->orWhereBetween('end_time', [
                            $spanStart->startOfDay(),
                            $spanEnd->endOfDay(),
                        ]);
                });
            },
            'member:id,name',
            'trainer:id,name',
        ])
            ->where(function($q) use ($spanStart, $spanEnd) {
                $q->whereBetween('start_date', [
                    $spanStart->toDateString(),
                    $spanEnd->toDateString(),
                ])
                    ->orWhereBetween('end_date', [
                        $spanStart->toDateString(),
                        $spanEnd->toDateString(),
                    ]);
            })
            ->get();


        return Inertia::render('Admin/Calendar/Index', [
            'weeks' => new CalendarWeekCollection($bookings)
        ]);
    }
}
