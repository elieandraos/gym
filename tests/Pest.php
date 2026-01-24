<?php

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\WorkoutSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, LazilyRefreshDatabase::class)->in('Http');
uses(TestCase::class, LazilyRefreshDatabase::class)->in('Console');
uses(TestCase::class, LazilyRefreshDatabase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect()->extend('toBeOne', function () {
//    return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function actingAsAdmin()
{
    $admin = User::factory()->create(['role' => Role::Admin]);

    return test()->actingAs($admin);
}

function setupUsersAndBookings(): void
{
    test()->artisan('db:seed', ['--class' => WorkoutSeeder::class]);

    $members = User::factory()->count(10)->create(['role' => Role::Member]);
    $trainers = User::factory()->count(2)->create(['role' => Role::Trainer]);

    /** @var User $member */
    foreach ($members as $member) {
        /** @var User $randomTrainer */
        $randomTrainer = $trainers->random();

        /** @var Booking $activeBooking */
        $activeBooking = Booking::factory()
            ->active()
            ->create([
                'member_id' => $member->id,
                'trainer_id' => $randomTrainer->id,
            ]);

        BookingSlot::factory()
            ->count($activeBooking->nb_sessions)
            ->forBooking($activeBooking)
            ->create();

        /** @var Booking $completedBooking */
        $completedBooking = Booking::factory()
            ->completed()
            ->create([
                'member_id' => $member->id,
                'trainer_id' => $randomTrainer->id,
            ]);

        BookingSlot::factory()
            ->count($completedBooking->nb_sessions)
            ->forBooking($completedBooking)
            ->create();
    }
}

function createSoonToExpireBooking(User $member, User $trainer, int $upcomingSessions = 2): Booking
{
    /** @var Booking $booking */
    $booking = Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 12,
        'start_date' => Carbon::today()->subDays(20),
        'end_date' => Carbon::today()->addDays(10),
    ]);

    $completedSessions = 12 - $upcomingSessions;

    // Create completed slots (in the past)
    for ($i = 0; $i < $completedSessions; $i++) {
        BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'start_time' => Carbon::today()->subDays(20 - $i)->setTime(10, 0),
            'end_time' => Carbon::today()->subDays(20 - $i)->setTime(11, 0),
            'status' => Status::Complete,
        ]);
    }

    // Create upcoming slots (in the future)
    for ($i = 0; $i < $upcomingSessions; $i++) {
        BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'start_time' => Carbon::today()->addDays(3 + ($i * 3))->setTime(10, 0),
            'end_time' => Carbon::today()->addDays(3 + ($i * 3))->setTime(11, 0),
            'status' => Status::Upcoming,
        ]);
    }

    return $booking->fresh(['member', 'trainer', 'bookingSlots']);
}
