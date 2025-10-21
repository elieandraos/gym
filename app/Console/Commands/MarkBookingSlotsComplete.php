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
        BookingSlot::query()
            ->whereDate('end_time', '=', Date::today())
            ->whereTime('end_time', '<', Date::now()->format('H:i:s'))
            ->whereNotIn('status', [Status::Complete, Status::Cancelled, Status::Frozen])
            ->update([
                'status' => Status::Complete,
            ]);
    }
}
