<?php

namespace App\Http\Resources\Calendar;

use App\Models\BookingSlot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use InvalidArgumentException;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match (true) {
            $this->resource instanceof BookingSlot => $this->fromBookingSlot(),
            is_array($this->resource) => $this->fromArray(),
            default => throw new InvalidArgumentException('Unsupported model type for calendar event: '.get_class($this->resource)),
        };
    }

    private function fromBookingSlot(): array
    {
        /** @var BookingSlot $slot */
        $slot = $this->resource;
        $minutes = $slot->start_time->minute;

        return [
            'id' => $slot->id,
            'start_time' => $slot->start_time->toIso8601String(),
            'end_time' => $slot->end_time->toIso8601String(),
            'title' => explode(' ', $slot->booking->member->name)[0].' - '.explode(' ', $slot->booking->trainer->name)[0],
            'url' => route('admin.bookings-slots.show', $slot->id),
            'meta_data' => [
                'member' => explode(' ', $slot->booking->member->name)[0],
                'member_photo_url' => $slot->booking->member->profile_photo_url,
                'trainer' => explode(' ', $slot->booking->trainer->name)[0],
                'trainer_color' => $slot->booking->trainer->color,
                'booking_id' => $slot->booking->id,
                'duration' => $slot->start_time->diffInMinutes($slot->end_time),
                'short_time' => $slot->start_time->format($minutes === 0 ? 'ga' : 'g:i a'),
            ],
        ];
    }

    private function fromArray(): array
    {
        return [
            'id' => $this['id'],
            'start_time' => $this['start_time']->toIso8601String(),
            'end_time' => $this['end_time']->toIso8601String(),
            'title' => $this['title'],
            'meta_data' => $this['meta_data'] ?? [],
        ];
    }
}
