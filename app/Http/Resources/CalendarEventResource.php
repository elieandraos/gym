<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time' => $this->end_time->toIso8601String(),
            'title' => $this->generateTitle(),
            'meta_data' => $this->generateMetaData(),
        ];
    }

    private function generateTitle(): string
    {
        $member = explode(' ', $this->booking->member->name)[0];
        $trainer = explode(' ', $this->booking->trainer->name)[0];

        return "{$member} - {$trainer}";
    }

    private function generateMetaData(): array
    {
        $minutes = $this->start_time->minute;

        return [
            'member' => explode(' ', $this->booking->member->name)[0],
            'trainer' => explode(' ', $this->booking->trainer->name)[0],
            'trainer_color' => $this->booking->trainer->color,
            'booking_id' => $this->booking->id,
            'duration' => $this->start_time->diffInMinutes($this->end_time),
            'short_time' => $this->start_time->format(
                $minutes === 0
                    ? 'ga'    // "7am", "2pm" when on the hour
                    : 'g:i a' // "7:30 am", "2:15 pm" when minutes > 0
            ),
        ];
    }
}