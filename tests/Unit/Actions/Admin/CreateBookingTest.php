<?php

use App\Actions\Admin\CreateBooking;
use App\Enums\Status;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

function bookingAttributes(array $overrides = []): array
{
    $member = User::factory()->member()->create();
    $trainer = User::factory()->trainer()->create();

    return array_merge([
        'nb_sessions' => 2,
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::today()->format('Y-m-d'),
        'end_date' => Carbon::today()->addDays(30)->format('Y-m-d'),
        'amount' => 270,
        'is_paid' => false,
        'schedule_days' => [['day' => 'Monday', 'time' => '10:00 am']],
        'booking_slots_dates' => [
            Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
            Carbon::today()->addDays(14)->format('Y-m-d H:i:s'),
        ],
    ], $overrides);
}

test('it creates a booking with its slots in the database', function () {
    $attributes = bookingAttributes();

    $booking = app(CreateBooking::class)->handle($attributes);

    $this->assertDatabaseHas(Booking::class, ['id' => $booking->id]);
    expect($booking->bookingSlots)->toHaveCount(2);
});

test('it sets slot status to Upcoming for future dates', function () {
    $attributes = bookingAttributes([
        'booking_slots_dates' => [Carbon::today()->addDays(7)->format('Y-m-d H:i:s')],
    ]);

    $booking = app(CreateBooking::class)->handle($attributes);

    expect($booking->bookingSlots->first()->status)->toBe(Status::Upcoming);
});

test('it sets slot status to Complete for past dates', function () {
    $attributes = bookingAttributes([
        'booking_slots_dates' => [Carbon::today()->subDays(7)->format('Y-m-d H:i:s')],
    ]);

    $booking = app(CreateBooking::class)->handle($attributes);

    expect($booking->bookingSlots->first()->status)->toBe(Status::Complete);
});

test('it sets end_date to the last slot date', function () {
    $lastDate = Carbon::today()->addDays(14);
    $attributes = bookingAttributes([
        'booking_slots_dates' => [
            Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
            $lastDate->format('Y-m-d H:i:s'),
        ],
    ]);

    $booking = app(CreateBooking::class)->handle($attributes);

    expect($booking->fresh()->end_date->toDateString())->toBe($lastDate->toDateString());
});

test('it returns the created Booking instance', function () {
    $result = app(CreateBooking::class)->handle(bookingAttributes());

    expect($result)->toBeInstanceOf(Booking::class);
});
