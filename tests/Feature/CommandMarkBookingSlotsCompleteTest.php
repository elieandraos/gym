<?php

use App\Enums\Status;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

it('marks past booking slots as complete', function () {
    $upcomingSlot = BookingSlot::factory()->create([
        'end_time' => Carbon::now()->addHour(),
        'status' => Status::Upcoming->value,
    ]);

    $cancelledSlot = BookingSlot::factory()->create([
        'end_time' => Carbon::now()->addHour(),
        'status' => Status::Cancelled->value,
    ]);

    // Travel in time to tomorrow and run the command
    Carbon::setTestNow(Carbon::tomorrow());
    Artisan::call('lift-station:mark-booking-slots-complete');

    // Assert
    expect($upcomingSlot->fresh()->status)->toBe(Status::Complete->value);
    expect($cancelledSlot->fresh()->status)->toBe(Status::Cancelled->value);

    // Reset time
    Carbon::setTestNow();
});
