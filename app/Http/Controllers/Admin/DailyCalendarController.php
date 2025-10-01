<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\DayEventsCollection;
use App\Models\Booking;
use App\Models\User;
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

        $selectedTrainerIds = $request->has('trainers')
            ? array_map('intval', array_filter(explode(',', $request->get('trainers'))))
            : [];

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $bookings = Booking::query()
            ->forCalendar($startOfDay, $endOfDay)
            ->when(! empty($selectedTrainerIds), function ($query) use ($selectedTrainerIds) {
                $query->whereIn('trainer_id', $selectedTrainerIds);
            })
            ->get();

        $events = $bookings->flatMap->bookingSlots
            ->filter(function ($slot) use ($startOfDay, $endOfDay) {
                return $slot->start_time >= $startOfDay && $slot->start_time <= $endOfDay;
            });

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

        return Inertia::render('Admin/DailyCalendar/Index', [
            'events' => new DayEventsCollection($events),
            'is_today' => $date->isSameDay(Carbon::today()),
            'available_trainers' => $trainers,
            'filters' => [
                'date' => $date->toDateString(),
                'trainers' => $selectedTrainerIds,
            ],
        ]);
    }
}
