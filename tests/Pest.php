<?php

use App\Enums\Role;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
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

uses(TestCase::class, LazilyRefreshDatabase::class)->in('Feature');

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

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

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
    $members = User::factory()->count(1)->create(['role' => Role::Member]);
    $trainers = User::factory()->count(1)->create(['role' => Role::Trainer]);

    $members->each(function ($user) use ($trainers) {
        // Create active booking
        $booking = Booking::factory()->active()->create([
            'member_id' => $user->id,
            'trainer_id' => $trainers->random()->id,
        ]);

        BookingSlot::factory($booking->nb_sessions)->forBooking($booking)->create();
    });
}
