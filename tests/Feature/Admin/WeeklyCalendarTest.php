<?php

use App\Enums\Role;
use App\Http\Resources\CalendarWeekCollection;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Support\Carbon;
//use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Carbon::setTestNow(Carbon::create(2024, 9, 2)); // Freeze to a known Monday
});

afterEach(function () {
    Carbon::setTestNow(); // Reset time after each test
});

test('admin can view the calendar with expected weekly data', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::now()->toDateString(),
        'end_date' => Carbon::now()->toDateString(),
    ]);

    BookingSlot::factory(2)->forBooking($booking)->create([
        'start_time' => Carbon::now()->addDay(),
        'end_time' => Carbon::now()->addDays(2),
    ]);

    // Load as controller does
    $booking->load([
        'member:id,name',
        'trainer:id,name',
        'bookingSlots' => fn ($q) => $q->orderBy('start_time'),
    ]);

    actingAsAdmin()
        ->get(route('admin.weekly-calendar.index'))
        ->assertOk()
        ->assertHasComponent('Admin/Calendar/Index')
        ->assertHasResource('weeks', new CalendarWeekCollection([$booking]));
});

// @todo: this will be added when implementing roles
//test('only admins can access the calendar page', function () {
//    $member = User::factory()->create(['role' => Role::Member]);
//    $trainer = User::factory()->create(['role' => Role::Trainer]);
//
//    get(route('admin.weekly-calendar.index'))->assertRedirect(route('login'));
//
//    actingAs($member)
//        ->get(route('admin.weekly-calendar.index'))
//        ->assertForbidden();
//
//    actingAs($trainer)
//        ->get(route('admin.weekly-calendar.index'))
//        ->assertForbidden();
//});

test('booking outside the 7-week window is not shown', function () {
    actingAsAdmin();

    $booking = Booking::factory()->create([
        'start_date' => Carbon::now()->subWeeks(10)->toDateString(),
        'end_date' => Carbon::now()->subWeeks(9)->toDateString(),
    ]);

    BookingSlot::factory()->forBooking($booking)->create([
        'start_time' => Carbon::now()->subWeeks(10)->startOfDay(),
        'end_time' => Carbon::now()->subWeeks(10)->endOfDay(),
    ]);

    get(route('admin.weekly-calendar.index'))
        ->assertHasComponent('Admin/Calendar/Index')
        ->assertHasResource('weeks', new CalendarWeekCollection([]));
});

test('booking with no slots is still included in weeks', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::now()->toDateString(),
        'end_date' => Carbon::now()->toDateString(),
    ]);

    $booking->load([
        'member:id,name',
        'trainer:id,name',
        'bookingSlots' => fn ($q) => $q->orderBy('start_time'),
    ]);

    actingAsAdmin()
        ->get(route('admin.weekly-calendar.index'))
        ->assertHasComponent('Admin/Calendar/Index')
        ->assertHasResource('weeks', new CalendarWeekCollection([$booking]));
});

test('multiple bookings appear in correct weeks', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking1 = Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::now()->toDateString(),
        'end_date' => Carbon::now()->toDateString(),
    ]);

    $booking2 = Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::now()->addWeeks(3)->toDateString(),
        'end_date' => Carbon::now()->addWeeks(3)->toDateString(),
    ]);

    BookingSlot::factory()->forBooking($booking1)->create([
        'start_time' => Carbon::now()->addDay(),
        'end_time' => Carbon::now()->addDays(2),
    ]);

    BookingSlot::factory()->forBooking($booking2)->create([
        'start_time' => Carbon::now()->addWeeks(3)->addDay(),
        'end_time' => Carbon::now()->addWeeks(3)->addDays(2),
    ]);

    $booking1->load(['member:id,name', 'trainer:id,name', 'bookingSlots']);
    $booking2->load(['member:id,name', 'trainer:id,name', 'bookingSlots']);

    actingAsAdmin()
        ->get(route('admin.weekly-calendar.index'))
        ->assertHasComponent('Admin/Calendar/Index')
        ->assertHasResource('weeks', new CalendarWeekCollection([$booking1, $booking2]));
});
