<?php

namespace App\Models\Traits;

trait UpdatesBookingEndDate
{
    /**
     * Update the booking's end_date to match the last booking slot's start_time.
     */
    public function updateEndDateToLastSlot(): self
    {
        $lastSlot = $this->bookingSlots()->orderBy('start_time', 'desc')->first();

        if ($lastSlot) {
            $this->update([
                'end_date' => $lastSlot->start_time->toDateString(),
            ]);
        }

        return $this;
    }
}
