<?php

namespace App\Http\Resources;

use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $upcomingSlot = $this->bookingSlots
            ->sortBy('start_time')
            ->firstWhere('status', Status::Upcoming->value);

        $completedSessionsCount = $this->bookingSlots
            ->where('status', Status::Complete->value)
            ->count();

        return [
            'id' => $this->id,
            'nb_sessions' => $this->nb_sessions,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'member' => new UserResource($this->whenLoaded('member')),
            'trainer' => new UserResource($this->whenLoaded('trainer')),
            'bookingSlots' => BookingSlotResource::collection($this->whenLoaded('bookingSlots')),
            'upcoming_session_date' => $upcomingSlot ? Carbon::parse($upcomingSlot->start_time)->isoFormat('ddd MMM Do') : null,
            'upcoming_session_time' => $upcomingSlot ? Carbon::parse($upcomingSlot->start_time)->isoFormat('h[h]mma') : null,
            'nb_completed_sessions' => $completedSessionsCount,
            'nb_remaining_sessions' => $this->nb_sessions - $completedSessionsCount,
        ];
    }
}
