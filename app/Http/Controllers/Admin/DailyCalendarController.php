<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\DayEventsCollection;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DailyCalendarController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = auth()->user();
        $today = Carbon::today();

        $date = $request->has('date')
            ? Carbon::parse($request->get('date'))
            : $today;

        // Use default trainer from settings if no trainers specified in URL
        $defaultTrainerId = $user->getSetting('calendar.default_trainer_id');
        $selectedTrainerIds = $request->has('trainers')
            ? array_map('intval', array_filter(explode(',', $request->get('trainers'))))
            : ($defaultTrainerId ? [$defaultTrainerId] : []);

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        /** @var Builder|Booking $query */
        $query = Booking::query();
        $bookings = $query
            ->forCalendar($startOfDay, $endOfDay)
            ->when(! empty($selectedTrainerIds), function ($q) use ($selectedTrainerIds) {
                $q->whereIn('trainer_id', $selectedTrainerIds);
            })
            ->get();

        $events = $bookings->flatMap->bookingSlots
            ->filter(function ($slot) use ($startOfDay, $endOfDay) {
                return $slot->start_time >= $startOfDay && $slot->start_time <= $endOfDay;
            });
        $events = new EloquentCollection($events);
        $events->load(['booking.member', 'booking.trainer']);

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

        // Convert 12-hour format with AM/PM to 24-hour format
        $startHour24 = $this->convertTo24Hour(
            $user->getSetting('calendar.start_hour', 6),
            $user->getSetting('calendar.start_period', 'AM')
        );
        $endHour24 = $this->convertTo24Hour(
            $user->getSetting('calendar.end_hour', 10),
            $user->getSetting('calendar.end_period', 'PM')
        );

        return Inertia::render('Admin/DailyCalendar/Index', [
            'events' => new DayEventsCollection($events),
            'is_today' => $date->isSameDay(Carbon::today()),
            'available_trainers' => $trainers,
            'filters' => [
                'date' => $date->toDateString(),
                'trainers' => $selectedTrainerIds,
            ],
            'calendar_settings' => [
                'start_hour' => $startHour24,
                'end_hour' => $endHour24,
            ],
        ]);
    }

    /**
     * Convert 12-hour format with AM/PM to 24-hour format
     */
    private function convertTo24Hour(int $hour, string $period): int
    {
        if ($period === 'AM') {
            return $hour === 12 ? 0 : $hour;
        } else {
            return $hour === 12 ? 12 : $hour + 12;
        }
    }
}
