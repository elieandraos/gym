<?php

namespace App\Http\Resources\Calendar;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DayEventsCollection extends ResourceCollection
{
    public function __construct($resource, protected Carbon $date)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        $events = $this->collection
            ->map(function ($eventArray) {
                return new EventResource($eventArray);
            })
            ->values();

        return [
            'date' => $this->date->toDateString(),
            'is_today' => $this->date->isSameDay(Carbon::today()),
            'events' => $events,
        ];
    }
}