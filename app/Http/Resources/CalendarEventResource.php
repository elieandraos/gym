<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'start_time' => $this['start_time']->toIso8601String(),
            'end_time' => $this['end_time']->toIso8601String(),
            'title' => $this['title'],
            'meta_data' => $this['meta_data'] ?? [],
        ];
    }
}