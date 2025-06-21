<?php

namespace App\Console\Commands;

use App\Enums\Status;
use App\Models\BookingSlot;
use Illuminate\Support\Facades\Date;
use Illuminate\Console\Command;

class MarkBookingSlotsComplete extends Command
{
    protected $signature = 'lift-station:mark-booking-slots-complete';

    protected $description = 'Updates past booking slots status to complete';

    public function handle(): void
    {
        BookingSlot::query()->where('end_time', '<', Date::now())
            ->whereNotIn('status', [Status::Complete, Status::Cancelled, Status::Frozen])
            ->update([
                'status' => Status::Complete,
            ]);
    }
}
