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

        $member = $slot->booking->member;
        $trainer = $slot->booking->trainer;
        $memberName = explode(' ', $member->name)[0];
        $trainerName = explode(' ', $trainer->name)[0];
        $memberPhotoUrl = $member->profile_photo_url;
        $trainerColor = $trainer->color;

        return [
            'id' => $slot->id,
            'start_time' => $slot->start_time->toIso8601String(),
            'end_time' => $slot->end_time->toIso8601String(),
            'title' => $memberName.' - '.$trainerName,
            'url' => route('admin.bookings-slots.show', $slot->id),
            'meta_data' => [
                'member' => $memberName,
                'member_photo_url' => $memberPhotoUrl,
                'trainer' => $trainerName,
                'trainer_color' => $trainerColor,
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
