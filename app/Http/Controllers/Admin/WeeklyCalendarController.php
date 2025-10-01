<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\WeekEventsCollection;
use App\Models\Booking;
use App\Models\User;
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

        $selectedTrainerIds = $request->has('trainers')
            ? array_map('intval', array_filter(explode(',', $request->get('trainers'))))
            : [];

        $bookings = Booking::query()
            ->forCalendar($start, $end)
            ->when(! empty($selectedTrainerIds), function ($query) use ($selectedTrainerIds) {
                $query->whereIn('trainer_id', $selectedTrainerIds);
            })
            ->get();

        $events = $bookings->flatMap->bookingSlots;

        $trainers = User::query()
            ->trainers()
            ->get()
            ->map(fn ($trainer) => [
                'id' => $trainer->id,
                'first_name' => explode(' ', $trainer->name)[0],
                'color' => $trainer->color,
            ])
            ->sortBy('first_name')
            ->values();

        return Inertia::render('Admin/Calendar/Index', [
            'events' => new WeekEventsCollection($events),
            'is_current' => $start->isSameWeek(Carbon::today()),
            'available_trainers' => $trainers,
            'filters' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'trainers' => $selectedTrainerIds,
            ],
        ]);
    }
}
