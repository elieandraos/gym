<?php

namespace App\Http\Resources;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
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
