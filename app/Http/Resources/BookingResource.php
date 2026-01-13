<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $today = Carbon::today();
        $status = 'scheduled';

        if ($this->start_date <= $today && $this->end_date >= $today) {
            $status = 'active';
        } elseif ($this->end_date < $today) {
            $status = 'completed';
        }

        return [
            'id' => $this->id,
            'nb_sessions' => $this->nb_sessions,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_paid' => $this->is_paid,
            'schedule_days' => $this->schedule_days,
            'is_frozen' => $this->is_frozen,
            'frozen_at' => $this->frozen_at,
            'status' => $status,
            'title' => Carbon::parse($this->start_date)->format('M j').' - '.Carbon::parse($this->end_date)->format('M j').', '.Carbon::parse($this->end_date)->format('Y'),
            'formatted_start_date' => Carbon::parse($this->start_date)->isoFormat('MMM Do'),
            'formatted_end_date' => Carbon::parse($this->end_date)->isoFormat('MMM Do'),
        ];
    }
}
