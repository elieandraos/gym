<?php

use App\Enums\Role;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Services\BookingManager;
use Carbon\Carbon;

it('generates repeatable dates correctly', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $repeatableDayTime = [
        ['day' => 'Monday', 'time' => '10:00 AM'],
        ['day' => 'Wednesday', 'time' => '02:00 PM'],
    ];

    $result = BookingManager::generateRepeatableDates($startDate, $nb_dates, $repeatableDayTime);

    expect($result)->toHaveCount($nb_dates);
    expect($result[0])->toBe('2023-06-05 10:00 AM');
    expect($result[1])->toBe('2023-06-07 02:00 PM');
    expect($result[2])->toBe('2023-06-12 10:00 AM');
    expect($result[3])->toBe('2023-06-14 02:00 PM');
    expect($result[4])->toBe('2023-06-19 10:00 AM');
});

it('throws an exception for invalid day when generating repeatable dates', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $repeatableDayTime = [
        ['day' => 'Fun day', 'time' => '10:00 AM'],
    ];

    BookingManager::generateRepeatableDates($startDate, $nb_dates, $repeatableDayTime);
})->throws(InvalidArgumentException::class, 'Invalid day in days array.');

it('throws an exception for invalid time format when generating repeatable dates', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $repeatableDayTime = [
        ['day' => 'Monday', 'time' => '25:00 AM'],
    ];

    BookingManager::generateRepeatableDates($startDate, $nb_dates, $repeatableDayTime);
})->throws(InvalidArgumentException::class, 'Invalid time format in days array.');

it('loads active bookings with slots for a member', function () {
    // Create a user (Member) and another user (Trainer)
    $member = User::factory()->create(['role' => Role::Member->value]);
    $trainer = User::factory()->create(['role' => Role::Trainer->value]);

    // Create an "active" booking: start_date <= now, end_date >= now
    $booking = Booking::factory()->create([
        'member_id'  => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::now()->subDay(),
        'end_date'   => Carbon::now()->addDay(),
    ]);

    // Create booking slots for this booking
    $slot1 = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => Carbon::now()->subHours(2),
        'end_time'   => Carbon::now()->subHour(),
    ]);
    $slot2 = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => Carbon::now()->addHour(),
        'end_time'   => Carbon::now()->addHours(2),
    ]);

    // Call the static method under test
    $loadedMember = BookingManager::loadActiveBookingsWithSlotsForUser($member);

    // Assert the 'memberBookings' relation is loaded, and we have 1 booking
    expect($loadedMember->relationLoaded('memberBookings'))->toBeTrue();
    expect($loadedMember->memberBookings)->toHaveCount(1);

    // Check the booking and its relationships
    $loadedBooking = $loadedMember->memberBookings->first();

    // The 'trainer' relation should be loaded
    expect($loadedBooking->relationLoaded('trainer'))->toBeTrue()
        ->and($loadedBooking->trainer->id)->toBe($trainer->id);

    // The bookingSlots are loaded in ascending order of start_time
    expect($loadedBooking->relationLoaded('bookingSlots'))->toBeTrue()
        ->and($loadedBooking->bookingSlots)->toHaveCount(2)
        ->and($loadedBooking->bookingSlots->first()->id)->toBe($slot1->id)
        ->and($loadedBooking->bookingSlots->last()->id)->toBe($slot2->id);
});

it('loads active bookings with slots for a trainer', function () {
    $trainer = User::factory()->create(['role' => Role::Trainer->value]);
    $member = User::factory()->create(['role' => Role::Member->value]);

    $booking = Booking::factory()->create([
        'member_id'  => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::now()->subDay(),
        'end_date'   => Carbon::now()->addDay(),
    ]);

    BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => Carbon::now()->subHours(1),
        'end_time'   => Carbon::now(),
    ]);


    $loadedTrainer = BookingManager::loadActiveBookingsWithSlotsForUser($trainer);

    expect($loadedTrainer->relationLoaded('trainerBookings'))->toBeTrue();
    expect($loadedTrainer->trainerBookings)->toHaveCount(1);

    $loadedBooking = $loadedTrainer->trainerBookings->first();

    expect($loadedBooking->relationLoaded('member'))->toBeTrue()
        ->and($loadedBooking->member->id)->toBe($member->id);

    expect($loadedBooking->relationLoaded('bookingSlots'))->toBeTrue()
        ->and($loadedBooking->bookingSlots)->toHaveCount(1);
});
