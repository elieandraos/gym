<?php

namespace App\Http\Resources;

use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin \App\Models\Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $slots = $this->relationLoaded('bookingSlots')
            ? $this->bookingSlots->sortBy('start_time')->values()
            : collect();

        $upcomingSlot = $slots->firstWhere('status', Status::Upcoming->value);
        $completedSessionsCount = $slots->where('status', Status::Complete->value)->count();

        return [
            'id' => $this->id,
            'nb_sessions' => $this->nb_sessions,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'title' => Carbon::parse($this->start_date)->format('M j').' - '.Carbon::parse($this->end_date)->format('M j').', '.Carbon::parse($this->end_date)->format('Y'),
            'formatted_end_date' => Carbon::parse($this->end_date)->isoFormat('MMM Do'),
            'member' => new UserResource($this->whenLoaded('member')),
            'trainer' => new UserResource($this->whenLoaded('trainer')),
            'bookingSlots' => $slots->isNotEmpty() ? BookingSlotResource::collection($slots) : null,
            'upcoming_session_url' => $upcomingSlot ? route('admin.bookings-slots.show', $upcomingSlot->id) : null,
            'upcoming_session_date' => $upcomingSlot ? Carbon::parse($upcomingSlot->start_time)->isoFormat('ddd MMM Do') : null,
            'upcoming_session_time' => $upcomingSlot ? Carbon::parse($upcomingSlot->start_time)->format('h:i A') : null,
            'nb_completed_sessions' => $completedSessionsCount,
            'nb_remaining_sessions' => $this->nb_sessions - $completedSessionsCount.' '.Str::plural('session', $this->nb_sessions - $completedSessionsCount),
        ];
    }
}
