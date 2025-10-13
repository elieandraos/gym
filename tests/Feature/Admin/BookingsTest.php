<?php

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingManager;
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
    $booking->load(['member', 'member.memberActiveBooking', 'trainer', 'bookingSlots' => function ($query) {
        $query->orderBy('start_time');
    }]);

    actingAsAdmin()
        ->get(route('admin.bookings.show', $booking))
        ->assertHasComponent('Admin/Bookings/Show')
        ->assertHasResource('booking', BookingResource::make($booking))
        ->assertStatus(200);
});

test('it creates a booking and its booking slots', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $data = [
        'start_date' => Carbon::today()->addMonths(2),
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 12,
        'is_paid' => true,
        'days' => [
            ['day' => 'Monday', 'time' => '07:00 am'],
            ['day' => 'Wednesday', 'time' => '07:00 am'],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', ['user' => $member->id]));

    $this->assertDatabaseHas(Booking::class, Arr::only($data, ['start_date', 'member_id', 'trainer_id', 'nb_sessions', 'is_paid']));

    // fetch the booking created
    $booking = Booking::query()->where('member_id', $member->id)
        ->where('trainer_id', $trainer->id)
        ->whereDate('start_date', Carbon::today()->addMonths(2))
        ->latest('created_at')
        ->firstOrFail();

    $this->assertNotNull($booking);

    // Generate expected session dates
    $expectedSessionDates = BookingManager::generateRepeatableDates($data['start_date'], $data['nb_sessions'], $data['days']);

    // Check that each expected session date is in the database
    foreach ($expectedSessionDates as $sessionDate) {
        $this->assertDatabaseHas('booking_slots', [
            'booking_id' => $booking->id,
            'start_time' => Carbon::parse($sessionDate)->format('Y-m-d H:i:s'),
        ]);
    }

    // check that the booking end date is equal to the last booking slot date
    $lastBookingSlot = $booking->bookingSlots()->orderBy('start_time', 'desc')->first();
    $this->assertNotNull($lastBookingSlot);
    $this->assertEquals($lastBookingSlot->start_time->toDateString(), $booking->end_date->toDateString());
});

test('it creates an unpaid booking', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $data = [
        'start_date' => Carbon::today()->addMonths(3),
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 8,
        'is_paid' => false,
        'days' => [
            ['day' => 'Tuesday', 'time' => '08:00 am'],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', ['user' => $member->id]));

    $this->assertDatabaseHas(Booking::class, Arr::only($data, ['start_date', 'member_id', 'trainer_id', 'nb_sessions', 'is_paid']));

    $booking = Booking::query()->where('member_id', $member->id)
        ->where('trainer_id', $trainer->id)
        ->whereDate('start_date', Carbon::today()->addMonths(3))
        ->latest('created_at')
        ->first();

    expect($booking->is_paid)->toBeFalse();
});

test('it can mark a booking as paid', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()
        ->unpaid()
        ->create([
            'member_id' => $member->id,
            'trainer_id' => $trainer->id,
        ]);

    expect($booking->is_paid)->toBeFalse();

    actingAsAdmin()
        ->patch(route('admin.bookings.mark-as-paid', $booking))
        ->assertSessionHas('success')
        ->assertRedirect();

    $booking->refresh();

    expect($booking->is_paid)->toBeTrue();
});

test('it loads create page with renew_from parameter and passes booking data', function () {
    $member = User::query()->members()->first();
    $trainer = User::query()->trainers()->first();

    $expiringBooking = createExpiringBooking($member, $trainer);

    $response = actingAsAdmin()
        ->get(route('admin.bookings.create', ['renew_from' => $expiringBooking->id]))
        ->assertStatus(200)
        ->assertHasComponent('Admin/Bookings/Create');

    $renewFromBooking = $response->viewData('page')['props']['renewFromBooking'];

    expect($renewFromBooking)->not->toBeNull();
    expect($renewFromBooking['id'])->toBe($expiringBooking->id);
    expect($renewFromBooking['member']['id'])->toBe($member->id);
    expect($renewFromBooking['trainer']['id'])->toBe($trainer->id);
    expect($renewFromBooking['schedule_days'])->not->toBeNull();
    expect($renewFromBooking['nb_sessions'])->toBe(12);
});

test('it saves schedule_days when creating a booking', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $scheduleDays = [
        ['day' => 'Monday', 'time' => '07:00 am'],
        ['day' => 'Wednesday', 'time' => '07:00 am'],
        ['day' => 'Friday', 'time' => '07:00 am'],
    ];

    $data = [
        'start_date' => Carbon::today()->addMonths(2),
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 12,
        'is_paid' => true,
        'days' => $scheduleDays,
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', ['user' => $member->id]));

    $booking = Booking::query()
        ->where('member_id', $member->id)
        ->where('trainer_id', $trainer->id)
        ->whereDate('start_date', Carbon::today()->addMonths(2))
        ->latest('created_at')
        ->firstOrFail();

    expect($booking->schedule_days)->not->toBeNull();
    expect($booking->schedule_days)->toBeArray();
    expect($booking->schedule_days)->toHaveCount(3);
    expect($booking->schedule_days[0]['day'])->toBe('Monday');
    expect($booking->schedule_days[1]['day'])->toBe('Wednesday');
    expect($booking->schedule_days[2]['day'])->toBe('Friday');
});

test('it creates a renewed booking with inherited schedule_days', function () {
    $member = User::query()->members()->first();
    $trainer = User::query()->trainers()->first();

    $expiringBooking = createExpiringBooking($member, $trainer);

    $originalScheduleDays = $expiringBooking->schedule_days;

    expect($originalScheduleDays)->not->toBeNull();

    // Start renewal booking 2 months after expiring booking ends to avoid overlap
    $renewalData = [
        'start_date' => Carbon::parse($expiringBooking->end_date)->addMonths(2),
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 12,
        'is_paid' => true,
        'days' => $originalScheduleDays,
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $renewalData)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', ['user' => $member->id]));

    $renewedBooking = Booking::query()
        ->where('member_id', $member->id)
        ->where('trainer_id', $trainer->id)
        ->whereDate('start_date', '>=', Carbon::parse($expiringBooking->end_date)->addMonth())
        ->latest('created_at')
        ->firstOrFail();

    expect($renewedBooking->id)->not->toBe($expiringBooking->id);
    expect($renewedBooking->schedule_days)->toEqual($originalScheduleDays);
    expect($renewedBooking->member_id)->toBe($expiringBooking->member_id);
    expect($renewedBooking->trainer_id)->toBe($expiringBooking->trainer_id);
    expect($renewedBooking->nb_sessions)->toBe(12);
});
