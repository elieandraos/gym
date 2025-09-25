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

        $events = $bookings->flatMap(function($booking) {
            return $booking->bookingSlots->map(function($slot) {
                $minutes = $slot->start_time->minute;

                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'title' => explode(' ', $slot->booking->member->name)[0] . ' - ' . explode(' ', $slot->booking->trainer->name)[0],
                    'meta_data' => [
                        'member' => explode(' ', $slot->booking->member->name)[0],
                        'trainer' => explode(' ', $slot->booking->trainer->name)[0],
                        'trainer_color' => $slot->booking->trainer->color,
                        'booking_id' => $slot->booking->id,
                        'duration' => $slot->start_time->diffInMinutes($slot->end_time),
                        'short_time' => $slot->start_time->format($minutes === 0 ? 'ga' : 'g:i a'),
                    ]
                ];
            });
        });

        return Inertia::render('Admin/Calendar/Index', [
            'week' => new CalendarWeekEventsCollection($events, $start, $end),
        ]);
    }
}
