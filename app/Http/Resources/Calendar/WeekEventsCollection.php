<?php

namespace App\Http\Resources\Calendar;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WeekEventsCollection extends ResourceCollection
{
    public function __construct($resource, protected Carbon $start, protected Carbon $end, protected $trainers = [])
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
            'start' => $this->start->toDateString(),
            'end' => $this->end->toDateString(),
            'is_current' => $this->start->isSameWeek(Carbon::today()),
            'trainers' => $this->trainers,
            'events' => $events,
        ];
    }
}
