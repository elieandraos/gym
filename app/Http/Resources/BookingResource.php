<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nb_sessions' => $this->nb_sessions,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'member' => new UserResource($this->whenLoaded('member')),
            'trainer' => new UserResource($this->whenLoaded('trainer')),
            'bookingSlots' => BookingSlotResource::collection($this->whenLoaded('bookingSlots')),
        ];
    }
}
