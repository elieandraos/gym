<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarDayEventsCollection;
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

        $bookings = Booking::with([
            'bookingSlots' => fn ($q) => $q->between($startOfDay, $endOfDay)->whereNot('status', Status::Cancelled),
            'member:id,name',
            'trainer:id,name,color',
        ])
            ->between($startOfDay, $endOfDay)
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

        return Inertia::render('Admin/DailyCalendar/Index', [
            'day' => new CalendarDayEventsCollection($events, $date),
        ]);
    }
}