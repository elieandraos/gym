<?php

namespace App\Console\Commands;

use App\Enums\Status;
use App\Models\BookingSlot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class MarkBookingSlotsComplete extends Command
{
    protected $signature = 'lift-station:mark-booking-slots-complete';

    protected $description = 'Updates past booking slots status to complete';

    public function handle(): void
    {
        $bookingSlots = BookingSlot::query()
            ->with(['booking.member'])
            ->where('end_time', '<', Date::now())
            ->whereNotIn('status', [Status::Complete, Status::Cancelled, Status::Frozen])
            ->get();

        if ($bookingSlots->isEmpty()) {
            $this->info('No booking slots were updated');

            return;
        }

        foreach ($bookingSlots as $bookingSlot) {
            $bookingSlot->update(['status' => Status::Complete]);

            $memberName = $bookingSlot->booking->member->name;
            $dateTime = $bookingSlot->start_time->format('Y-m-d H:i');

            $this->info("$memberName booking slot ($dateTime) set to complete");
        }
    }
}
