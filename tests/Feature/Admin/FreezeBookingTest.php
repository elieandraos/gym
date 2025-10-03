<?php

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it requires authentication', function () {
    $booking = Booking::query()->first();

    $this->get(route('admin.bookings.freeze.index', $booking))
        ->assertRedirect(route('login'));

    $this->patch(route('admin.bookings.freeze.update', $booking))
        ->assertRedirect(route('login'));
});

test('it renders the freeze booking confirmation page', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $booking->load(['member', 'trainer']);

    actingAsAdmin()
        ->get(route('admin.bookings.freeze.index', $booking))
        ->assertHasComponent('Admin/FreezeBooking/Index')
        ->assertStatus(200);
});

test('it freezes a booking successfully', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    BookingSlot::factory()->count(3)->create([
        'booking_id' => $booking->id,
        'status' => Status::Upcoming,
        'start_time' => Carbon::now()->addDays(1),
        'end_time' => Carbon::now()->addDays(1)->addHour(),
    ]);

    BookingSlot::factory()->count(2)->create([
        'booking_id' => $booking->id,
        'status' => Status::Complete,
        'start_time' => Carbon::now()->subDays(3),
        'end_time' => Carbon::now()->subDays(3)->addHour(),
    ]);

    expect($booking->is_frozen)->toBeFalse();
    expect($booking->frozen_at)->toBeNull();

    actingAsAdmin()
        ->patch(route('admin.bookings.freeze.update', $booking))
        ->assertSessionHas('flash.banner', 'Booking frozen successfully')
        ->assertSessionHas('flash.bannerStyle', 'success')
        ->assertRedirect(route('admin.members.show', $member->id));

    $booking->refresh();

    expect($booking->is_frozen)->toBeTrue();
    expect($booking->frozen_at)->not->toBeNull();
    expect($booking->frozen_at)->toBeInstanceOf(Carbon::class);
});

test('it updates upcoming slots to frozen status', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $upcomingSlots = BookingSlot::factory()->count(3)->create([
        'booking_id' => $booking->id,
        'status' => Status::Upcoming,
        'start_time' => Carbon::now()->addDays(1),
        'end_time' => Carbon::now()->addDays(1)->addHour(),
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings.freeze.update', $booking));

    foreach ($upcomingSlots as $slot) {
        $slot->refresh();
        expect($slot->status)->toBe(Status::Frozen);
    }
});

test('it does not update completed or cancelled slots', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $completedSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'status' => Status::Complete,
        'start_time' => Carbon::now()->subDays(3),
        'end_time' => Carbon::now()->subDays(3)->addHour(),
    ]);

    $cancelledSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'status' => Status::Cancelled,
        'start_time' => Carbon::now()->addDays(2),
        'end_time' => Carbon::now()->addDays(2)->addHour(),
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings.freeze.update', $booking));

    $completedSlot->refresh();
    $cancelledSlot->refresh();

    expect($completedSlot->status)->toBe(Status::Complete);
    expect($cancelledSlot->status)->toBe(Status::Cancelled);
});

test('it cannot freeze an already frozen booking', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'is_frozen' => true,
        'frozen_at' => Carbon::now()->subDays(2),
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings.freeze.update', $booking))
        ->assertSessionHas('flash.banner', 'This booking is already frozen')
        ->assertSessionHas('flash.bannerStyle', 'danger')
        ->assertRedirect();
});
