<?php

use App\Helpers\DateHelper;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it requires authentication', function () {
    $booking = Booking::query()->first();

    $this->get(route('admin.bookings.show', $booking))->assertRedirect(route('login'));
});

test('it renders the create booking page', function () {
    actingAsAdmin()
        ->get(route('admin.bookings.create'))
        ->assertHasComponent('Admin/Bookings/Create')
        ->assertStatus(200);
});

test('it validates request before creating a booking', function () {
    $data = [
        'member_id' => null,
        'trainer_id' => null,
        'start_date' => null,
        'nb_sessions' => null,
        'days' => [],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasErrors([
            'member_id',
            'trainer_id',
            'start_date',
            'nb_sessions',
            'days',
        ])
        ->assertStatus(302);
});

test('it shows booking information', function () {
    $booking = Booking::query()->first();
    $booking->load(['member', 'trainer', 'bookingSlots' => function ($query) {
        $query->orderBy('start_time');
    }]);

    actingAsAdmin()
        ->get(route('admin.bookings.show', $booking))
        ->assertHasComponent('Admin/Bookings/Show')
        ->assertHasResource('booking', BookingResource::make($booking))
        ->assertStatus(200);
});

test('it creates a booking and its booking slots', function () {
    $member = User::members()->inRandomOrder()->first();
    $trainer = User::trainers()->inRandomOrder()->first();

    $data = [
        'start_date' => Carbon::today()->addMonths(2),
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 12,
        'days' => [
            ['day' => 'Monday', 'time' => '07:00 am'],
            ['day' => 'Wednesday', 'time' => '07:00 am'],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.show', ['role' => $member->role, 'user' => $member->id]));

    $this->assertDatabaseHas(Booking::class, Arr::only($data, ['start_date', 'member_id', 'trainer_id', 'nb_sessions']));

    // fetch the booking created
    $booking = Booking::where('member_id', $member->id)
        ->where('trainer_id', $trainer->id)
        ->whereDate('start_date', Carbon::today()->addMonths(2))
        ->latest('created_at')
        ->firstOrFail();

    // Generate expected session dates
    $expectedSessionDates = DateHelper::generateRepeatableDates($data['start_date'], $data['nb_sessions'], $data['days']);

    // Check that each expected session date is in the database
    foreach ($expectedSessionDates as $sessionDate) {
        $this->assertDatabaseHas('booking_slots', [
            'booking_id' => $booking->id,
            'start_time' => Carbon::parse($sessionDate)->format('Y-m-d H:i:s'),
        ]);
    }
});
