<?php

use App\Models\Booking;

beforeEach(function () {
    setupUsersAndBookings();
});

test('delete/destroy routes require authentication', function () {
    $booking = Booking::query()->first();

    $this->get(route('admin.bookings.delete', $booking))->assertRedirect(route('login'));
    $this->delete(route('admin.bookings.destroy', $booking))->assertRedirect(route('login'));
});

test('it renders the delete training confirmation page', function () {
    $booking = Booking::query()->active()->with('member')->first();

    actingAsAdmin()
        ->get(route('admin.bookings.delete', $booking))
        ->assertHasComponent('Admin/DeleteTraining/Index')
        ->assertHasProp('booking.id', $booking->id)
        ->assertHasProp('member.id', $booking->member->id)
        ->assertStatus(200);
});

test('it deletes the training and redirects to the member profile', function () {
    $booking = Booking::query()->active()->with('member')->first();

    actingAsAdmin()
        ->delete(route('admin.bookings.destroy', $booking))
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.bannerStyle', 'success')
        ->assertRedirect(route('admin.members.show', $booking->member->id));
});
