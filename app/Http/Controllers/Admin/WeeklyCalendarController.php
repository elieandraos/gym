<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarWeekEventsCollection;
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

        $bookings = Booking::with([
            'bookingSlots' => fn ($q) => $q->between($start, $end)->whereNot('status', Status::Cancelled),
            'member:id,name',
            'trainer:id,name,color',
        ])
            ->between($start, $end)
            ->get();

        $slots = $bookings->flatMap->bookingSlots;

        return Inertia::render('Admin/Calendar/Index', [
            'week' => new CalendarWeekEventsCollection($slots, $start, $end),
        ]);
    }
}
