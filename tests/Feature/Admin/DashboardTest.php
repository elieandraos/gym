<?php

use App\Enums\Role;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

test('it shows expiring bookings with 2 remaining sessions', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $expiringBooking = createSoonToExpireBooking($member, $trainer);

    actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('bookings.expiring', 1)
            ->where('bookings.expiring.0.id', $expiringBooking->id)
        );
});

test('it shows expiring bookings with 1 remaining session', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $expiringBooking = createSoonToExpireBooking($member, $trainer, 1);

    actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('bookings.expiring', 1)
            ->where('bookings.expiring.0.id', $expiringBooking->id)
        );
});

test('it excludes expiring bookings when member has a scheduled booking', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    // Create expiring booking (2 remaining sessions)
    $expiringBooking = createSoonToExpireBooking($member, $trainer);

    // Create scheduled booking for same member (starts in future)
    $scheduledBooking = Booking::factory()->scheduled()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::today()->addMonths(1),
    ]);

    // Expiring booking should NOT be in the list because member has scheduled booking
    actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('bookings.expiring', 0)
        );
});

test('it shows expiring bookings when member has no scheduled booking', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    // Create expiring booking (2 remaining sessions)
    $expiringBooking = createSoonToExpireBooking($member, $trainer);

    // Expiring booking SHOULD be in the list
    actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('bookings.expiring', 1)
            ->where('bookings.expiring.0.id', $expiringBooking->id)
        );
});
