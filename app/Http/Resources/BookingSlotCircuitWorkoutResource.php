<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSlotCircuitWorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'circuit_id' => $this->booking_slot_circuit_id,
            'name' => $this->whenLoaded('workout', fn () => $this->workout->name),
            'category' => $this->whenLoaded('workout', fn () => $this->workout->category->value),
            'sets' => $this->whenLoaded('sets', function () {
                return $this->sets->map(fn ($set) => [
                    'reps' => $set->reps,
                    'weight_in_kg' => $set->weight_in_kg,
                    'duration_in_seconds' => $set->duration_in_seconds,
                ]);
            }),
        ];
    }
}
