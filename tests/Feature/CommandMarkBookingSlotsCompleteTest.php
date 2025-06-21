<?php

use App\Enums\Status;
use App\Models\BookingSlot;
use Illuminate\Support\Facades\Artisan;

beforeEach(function(){
    $this->travelTo(now()); // freeze “now” at the start of the test
});

it('marks past booking slots as complete', function () {
    $upcomingSlot = BookingSlot::factory()->create([
        'end_time' => now()->addHour(),
        'status' => Status::Upcoming,
    ]);

    $cancelledSlot = BookingSlot::factory()->create([
        'end_time' => now()->addHour(),
        'status' => Status::Cancelled,
    ]);

    // Travel in time to tomorrow and run the command
    $this->travelTo(now()->addDay());
    Artisan::call('lift-station:mark-booking-slots-complete');

    // Assert
    expect($upcomingSlot->fresh()->status)->toBe(Status::Complete);
    expect($cancelledSlot->fresh()->status)->toBe(Status::Cancelled);

    // Reset time
    $this->travelBack();
});
