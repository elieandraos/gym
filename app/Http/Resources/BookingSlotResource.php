<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSlotResource extends JsonResource
{
    /** @mixin \App\Models\BookingSlot */
    public function toArray(Request $request): array
    {
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        return [
            'id' => $this->id,
            'date' => $startTime->clone()->toDate(),
            'start_time' => $startTime->format('h:i A'),
            'end_time' => $endTime->format('h:i A'),
            'status' => $this->status,
            'formatted_date' => $this->formatDateWithSuffix($startTime),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'badge_type' => match($this->status) {
                'upcoming' => 'warning',
                'cancelled' => 'error',
                'frozen' => 'info',
                default => 'success',
            },
            ''
        ];
    }

    private function formatDateWithSuffix(Carbon $date): string
    {
        $day = $date->day;
        $suffix = $this->getDaySuffix($day);

        return $date->format('D, M j').$suffix;
    }

    private function getDaySuffix(int $day): string
    {
        if (! in_array(($day % 100), [11, 12, 13])) {
            switch ($day % 10) {
                case 1:
                    return 'st';
                case 2:
                    return 'nd';
                case 3:
                    return 'rd';
            }
        }

        return 'th';
    }
}
