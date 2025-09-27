<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\DayEventsCollection;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DailyCalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();

        $date = $request->has('date')
            ? Carbon::parse($request->get('date'))
            : $today;

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $events = Booking::query()->forCalendar($startOfDay, $endOfDay)->get()->flatMap->bookingSlots
            ->filter(function ($slot) use ($startOfDay, $endOfDay) {
                return $slot->start_time >= $startOfDay && $slot->start_time <= $endOfDay;
            });

        return Inertia::render('Admin/DailyCalendar/Index', [
            'day' => new DayEventsCollection($events, $date),
        ]);
    }
}
