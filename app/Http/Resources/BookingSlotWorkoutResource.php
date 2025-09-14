<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BookingSlotWorkout */
class BookingSlotWorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_slot_id' => $this->booking_slot_id,
            'name' => $this->whenLoaded('workout', fn () => $this->workout->name),
            'category' => $this->whenLoaded('workout', fn () => $this->workout->category->value),
            'sets' => $this->whenLoaded('sets', function () {
                return $this->sets->map(fn ($set) => [
                    'reps' => $set->reps,
                    'weight_in_kg' => $set->weight_in_kg,
                    'duration_in_seconds' => $set->duration_in_seconds,
                ]);
            }),
            'delete_url' => route('admin.bookings-slots.workout.destroy', $this->id),
        ];
    }
}
