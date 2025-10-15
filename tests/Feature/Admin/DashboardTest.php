<?php

use App\Enums\Role;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

test('it shows expiring bookings with 2 remaining sessions', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $expiringBooking = createExpiringBooking($member, $trainer);

    $response = actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200);

    $expiringBookings = $response->viewData('page')['props']['bookings']['expiring'];

    expect($expiringBookings)->toBeArray()
        ->and($expiringBookings)->toHaveCount(1)
        ->and($expiringBookings[0]['id'])->toBe($expiringBooking->id);
});

test('it shows expiring bookings with 1 remaining session', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $expiringBooking = createExpiringBooking($member, $trainer, 1);

    $response = actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200);

    $expiringBookings = $response->viewData('page')['props']['bookings']['expiring'];

    expect($expiringBookings)->toBeArray()
        ->and($expiringBookings)->toHaveCount(1)
        ->and($expiringBookings[0]['id'])->toBe($expiringBooking->id);
});

test('it excludes expiring bookings when member has a scheduled booking', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    // Create expiring booking (2 remaining sessions)
    $expiringBooking = createExpiringBooking($member, $trainer);

    // Create scheduled booking for same member (starts in future)
    $scheduledBooking = Booking::factory()->scheduled()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::today()->addMonths(1),
    ]);

    $response = actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200);

    $expiringBookings = $response->viewData('page')['props']['bookings']['expiring'];

    // Expiring booking should NOT be in the list because member has scheduled booking
    expect($expiringBookings)->toBeArray()
        ->and($expiringBookings)->toHaveCount(0);
});

test('it shows expiring bookings when member has no scheduled booking', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    // Create expiring booking (2 remaining sessions)
    $expiringBooking = createExpiringBooking($member, $trainer);

    // No scheduled booking created

    $response = actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200);

    $expiringBookings = $response->viewData('page')['props']['bookings']['expiring'];

    // Expiring booking SHOULD be in the list
    expect($expiringBookings)->toBeArray()
        ->and($expiringBookings)->toHaveCount(1)
        ->and($expiringBookings[0]['id'])->toBe($expiringBooking->id);
});
