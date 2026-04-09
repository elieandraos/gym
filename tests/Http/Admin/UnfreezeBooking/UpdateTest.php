<?php

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('unfreeze update route requires authentication', function () {
    $booking = Booking::query()->first();

    $this->patch(route('admin.bookings.unfreeze.update', $booking))
        ->assertRedirect(route('login'));
});

test('it unfreezes a booking successfully', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'is_frozen' => true,
        'frozen_at' => Carbon::now()->subDays(5),
    ]);

    $frozenSlots = BookingSlot::factory()->count(3)->create([
        'booking_id' => $booking->id,
        'status' => Status::Frozen,
        'start_time' => Carbon::now()->addDays(1),
        'end_time' => Carbon::now()->addDays(1)->addHour(),
    ]);

    $newDate1 = Carbon::now()->addDays(10);
    $newDate2 = Carbon::now()->addDays(12);
    $newDate3 = Carbon::now()->addDays(14);

    $data = [
        'slots' => [
            [
                'id' => $frozenSlots[0]->id,
                'start_time' => $newDate1->format('Y-m-d').' 07:00:00',
                'end_time' => $newDate1->format('Y-m-d').' 08:00:00',
            ],
            [
                'id' => $frozenSlots[1]->id,
                'start_time' => $newDate2->format('Y-m-d').' 07:00:00',
                'end_time' => $newDate2->format('Y-m-d').' 08:00:00',
            ],
            [
                'id' => $frozenSlots[2]->id,
                'start_time' => $newDate3->format('Y-m-d').' 07:00:00',
                'end_time' => $newDate3->format('Y-m-d').' 08:00:00',
            ],
        ],
    ];

    actingAsAdmin()
        ->patch(route('admin.bookings.unfreeze.update', $booking), $data)
        ->assertSessionHas('flash.banner', 'Booking unfrozen successfully')
        ->assertSessionHas('flash.bannerStyle', 'success')
        ->assertRedirect(route('admin.members.show', $member->id));
});

test('it validates required slot data', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'is_frozen' => true,
        'frozen_at' => Carbon::now()->subDays(5),
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings.unfreeze.update', $booking), [])
        ->assertSessionHasErrors(['slots']);
});
