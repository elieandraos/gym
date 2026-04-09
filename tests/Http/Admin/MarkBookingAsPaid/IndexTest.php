<?php

use App\Models\Booking;

beforeEach(function () {
    setupUsersAndBookings();
});

test('mark as paid route requires authentication', function () {
    $booking = Booking::query()->first();

    $this->patch(route('admin.bookings.mark-as-paid', $booking))
        ->assertRedirect(route('login'));
});

test('it marks an unpaid booking as paid', function () {
    $booking = Booking::query()->where('is_paid', false)->first();

    expect($booking)->not()->toBeNull()
        ->and($booking->is_paid)->toBeFalse();

    actingAsAdmin()
        ->patch(route('admin.bookings.mark-as-paid', $booking))
        ->assertRedirect()
        ->assertSessionHas('success', "{$booking->member->first_name}'s training has been marked as paid.");
});

test('it can mark an already paid booking as paid without issues', function () {
    $booking = Booking::query()->where('is_paid', true)->first();

    expect($booking)->not()->toBeNull()
        ->and($booking->is_paid)->toBeTrue();

    actingAsAdmin()
        ->patch(route('admin.bookings.mark-as-paid', $booking))
        ->assertRedirect()
        ->assertSessionHas('success', "{$booking->member->first_name}'s training has been marked as paid.");
});

test('it redirects back to the previous page after marking as paid', function () {
    $booking = Booking::query()->where('is_paid', false)->first();

    expect($booking)->not()->toBeNull();

    $previousUrl = route('admin.members.show', $booking->member);

    actingAsAdmin()
        ->from($previousUrl)
        ->patch(route('admin.bookings.mark-as-paid', $booking))
        ->assertRedirect($previousUrl);
});

test('it handles non-existent booking gracefully', function () {
    actingAsAdmin()
        ->patch(route('admin.bookings.mark-as-paid', 999))
        ->assertNotFound();
});
