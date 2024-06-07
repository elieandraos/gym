<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSlotResource extends JsonResource
{
    /** @mixin \App\Models\BookingSlot */
    public function toArray(Request $request): array
    {
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        return [
            'id' => $this->id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'date' => $startTime->format('l, F j'),
            'time' => $startTime->format('g\hiA') . ' - ' . $endTime->format('g\hiA'),
        ];
    }
}
