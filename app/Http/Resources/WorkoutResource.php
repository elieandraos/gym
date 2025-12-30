<?php

namespace App\Http\Resources;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
    /**
     * Convert the workout resource into an array suitable for API responses.
     *
     * @param Request $request The incoming HTTP request instance.
     * @return array Associative array with keys `id`, `name`, and `categories` representing the workout.
     */
    public function toArray(Request $request): array
    {
        /** @var Workout $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'categories' => $this->categories,
        ];
    }
}