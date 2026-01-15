<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\WeekEventsCollection;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeeklyCalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Get calendar settings from user
        $startDayOfWeek = $this->getDayOfWeekNumber(day: $user->getSetting('calendar.start_day', 'monday'));
        $endDayOfWeek = $this->getDayOfWeekNumber(day: $user->getSetting('calendar.end_day', 'saturday'));

        // Calculate the number of days to show (inclusive)
        $daysToShow = $endDayOfWeek >= $startDayOfWeek
            ? ($endDayOfWeek - $startDayOfWeek)
            : (7 - $startDayOfWeek + $endDayOfWeek);

        $start = $request->has('start')
            ? Carbon::parse($request->get('start'))
            : $today->copy()->startOfWeek($startDayOfWeek);

        $end = $request->has('end')
            ? Carbon::parse($request->get('end'))
            : $start->copy()->addDays($daysToShow);

        // Use default trainer from settings if no trainers specified in URL
        $defaultTrainerId = $user->getSetting('calendar.default_trainer_id');
        $selectedTrainerIds = $request->has('trainers')
            ? array_map('intval', array_filter(explode(',', $request->get('trainers'))))
            : ($defaultTrainerId ? [$defaultTrainerId] : []);

        $bookings = Booking::query()
            ->forCalendar($start, $end)
            ->when(! empty($selectedTrainerIds), function ($query) use ($selectedTrainerIds) {
                $query->whereIn('trainer_id', $selectedTrainerIds);
            })
            ->get();

        $events = new EloquentCollection($bookings->flatMap->bookingSlots);
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

        $startHour24 = $this->convertTo24Hour(
            $user->getSetting('calendar.start_hour', 6),
            $user->getSetting('calendar.start_period', 'AM')
        );
        $endHour24 = $this->convertTo24Hour(
            $user->getSetting('calendar.end_hour', 10),
            $user->getSetting('calendar.end_period', 'PM')
        );

        return Inertia::render('Admin/Calendar/Index', [
            'events' => new WeekEventsCollection($events),
            'is_current' => $start->isSameWeek(Carbon::today()),
            'available_trainers' => $trainers,
            'filters' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'trainers' => $selectedTrainerIds,
            ],
            'calendar_settings' => [
                'start_hour' => $startHour24,
                'end_hour' => $endHour24,
            ],
        ]);
    }

    private function getDayOfWeekNumber(string $day): int
    {
        return match (strtolower($day)) {
            'sunday' => CarbonInterface::SUNDAY,
            'tuesday' => CarbonInterface::TUESDAY,
            'wednesday' => CarbonInterface::WEDNESDAY,
            'thursday' => CarbonInterface::THURSDAY,
            'friday' => CarbonInterface::FRIDAY,
            'saturday' => CarbonInterface::SATURDAY,
            default => CarbonInterface::MONDAY,
        };
    }

    private function convertTo24Hour(int $hour, string $period): int
    {
        if ($period === 'AM') {
            return $hour === 12 ? 0 : $hour;
        } else {
            return $hour === 12 ? 12 : $hour + 12;
        }
    }
}
