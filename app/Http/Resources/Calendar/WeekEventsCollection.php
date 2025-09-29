<?php

namespace App\Http\Resources\Calendar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WeekEventsCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(function ($eventArray) {
                return new EventResource($eventArray);
            })
            ->values()
            ->toArray();
    }
}
