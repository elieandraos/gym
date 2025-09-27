<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\WeekEventsCollection;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeeklyCalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();

        $start = $request->has('start')
            ? Carbon::parse($request->get('start'))
            : $today->copy()->startOfWeek();

        $end = $request->has('end')
            ? Carbon::parse($request->get('end'))
            : $start->copy()->addDays(5);

        $events = Booking::query()->forCalendar($start, $end)->get()->flatMap->bookingSlots;

        return Inertia::render('Admin/Calendar/Index', [
            'events' => new WeekEventsCollection($events, $start, $end),
        ]);
    }
}
