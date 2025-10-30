<?php

use App\Enums\Status;
use App\Models\BookingSlot;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->travelTo(now()); // freeze “now” at the start of the test
});

it('marks past booking slots as complete', function () {
    $upcomingSlot = BookingSlot::factory()->create([
        'end_time' => now()->subHour(), // 1 hour ago
        'status' => Status::Upcoming,
    ]);

    $cancelledSlot = BookingSlot::factory()->create([
        'end_time' => now()->subHour(), // 1 hour ago
        'status' => Status::Cancelled,
    ]);

    Artisan::call('lift-station:mark-booking-slots-complete');

    // Assert
    expect($upcomingSlot->fresh()->status)->toBe(Status::Complete)
        ->and($cancelledSlot->fresh()->status)->toBe(Status::Cancelled);

    // Reset time
    $this->travelBack();
});

it('does not mark future booking slots as complete', function () {
    $slot = BookingSlot::factory()->create([
        'end_time' => now()->addHour(),
        'status' => Status::Upcoming,
    ]);

    Artisan::call('lift-station:mark-booking-slots-complete');

    expect($slot->fresh()->status)->toBe(Status::Upcoming);

    $this->travelBack();
});

it('only processes booking slots from today', function () {
    // Create a slot from yesterday that should NOT be processed
    $yesterdaySlot = BookingSlot::factory()->create([
        'end_time' => now()->subDay()->setTime(10, 0),
        'status' => Status::Upcoming,
    ]);

    // Create a slot from today that SHOULD be processed
    $todaySlot = BookingSlot::factory()->create([
        'end_time' => now()->subHour(),
        'status' => Status::Upcoming,
    ]);

    Artisan::call('lift-station:mark-booking-slots-complete');

    // Today's past slot should be marked complete
    expect($todaySlot->fresh()->status)->toBe(Status::Complete)
        ->and($yesterdaySlot->fresh()->status)->toBe(Status::Upcoming);

    // Yesterday's slot should NOT be touched (optimization - only processes today)

    $this->travelBack();
});
