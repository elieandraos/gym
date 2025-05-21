<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Carbon\CarbonInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class CalendarWeekCollection extends ResourceCollection
{
    public function toArray(Request $request) : array
    {
        // Compute the 7-week span: 3 weeks back, current Monday→Saturday, 3 ahead
        $today      = Carbon::today();
        $monday     = $today->copy()->startOfWeek(CarbonInterface::MONDAY);
        $saturday   = $monday->copy()->addDays(5);
        $spanStart  = $monday->copy()->subWeeks(3);
        $spanEnd    = $saturday->copy()->addWeeks(3);

        $weeks = [];
        $cursor = $spanStart->copy();

        while ($cursor->lte($spanEnd)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $weekStart->copy()->addDays(5);

            // Filter bookings that have slots in this week, then map to minimal structure
            $weekBookings = $this->collection
                ->filter(function($booking) use ($weekStart, $weekEnd) {
                    return $booking->bookingSlots->contains(function($slot) use ($weekStart, $weekEnd) {
                        return $slot->start_time->between(
                            $weekStart->startOfDay(),
                            $weekEnd->endOfDay()
                        );
                    });
                })
                ->map(function($booking) use ($weekStart, $weekEnd) {
                    $slots = $booking->bookingSlots
                        ->filter(function($slot) use ($weekStart, $weekEnd) {
                            return $slot->start_time->between(
                                $weekStart->startOfDay(),
                                $weekEnd->endOfDay()
                            );
                        })
                        ->values()
                        ->map(function($slot) {
                            return [
                                'id'         => $slot->id,
                                'start_time'=> $slot->start_time->toIso8601String(),
                                'end_time'  => $slot->end_time->toIso8601String(),
                            ];
                        });

                    return [
                        'member' =>  $booking->member->name,
                        'trainer' =>  $booking->trainer->name,
                        'booking_slots' => $slots,
                    ];
                })
                ->values();

            $weeks[] = [
                'start'    => $weekStart->toDateString(),
                'end'      => $weekEnd->toDateString(),
                'is_current' => Carbon::today()->between($weekStart->copy()->startOfDay(), $weekEnd->copy()->endOfDay()),
                'bookings' => $weekBookings,
            ];

            $cursor->addWeek();
        }

        return  $weeks;
    }
}
