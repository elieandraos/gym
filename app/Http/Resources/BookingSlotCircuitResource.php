<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSlotCircuitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'workouts' => $this->whenLoaded('circuitWorkouts',
                fn () => BookingSlotCircuitWorkoutResource::collection($this->circuitWorkouts)
            ),
        ];
    }
}
