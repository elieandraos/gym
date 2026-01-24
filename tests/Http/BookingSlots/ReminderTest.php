<?php

use App\Console\Commands\SendBookingSlotReminders;
use App\Enums\Status;
use App\Mail\Member\BookingSlotReminderEmail;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('it sends reminder emails for booking slots scheduled tomorrow', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    Artisan::call(SendBookingSlotReminders::class);

    Mail::assertQueued(BookingSlotReminderEmail::class, function ($mail) use ($member) {
        return $mail->hasTo($member->email);
    });
});

test('it does not send reminders for booking slots not scheduled tomorrow', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    // Create a slot for today
    $today = Date::today()->setHour(14)->setMinute(0);
    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $today,
            'end_time' => $today->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    // Create a slot for next week
    $nextWeek = Date::now()->addWeek()->setHour(14)->setMinute(0);
    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $nextWeek,
            'end_time' => $nextWeek->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    Artisan::call(SendBookingSlotReminders::class);

    Mail::assertNotQueued(BookingSlotReminderEmail::class);
});

test('it does not send reminders for cancelled or frozen booking slots', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);

    // Create cancelled slot
    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Cancelled,
        ]);

    // Create frozen slot
    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow->copy()->addHour(),
            'end_time' => $tomorrow->copy()->addHours(2),
            'status' => Status::Frozen,
        ]);

    Artisan::call(SendBookingSlotReminders::class);

    Mail::assertNotQueued(BookingSlotReminderEmail::class);
});

test('reminder email is queued and not sent immediately', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    Artisan::call(SendBookingSlotReminders::class);

    Mail::assertQueued(BookingSlotReminderEmail::class);
    Mail::assertNotSent(BookingSlotReminderEmail::class);
});

test('reminder email contains member first name', function () {
    $member = User::factory()->create(['name' => 'John Smith']);
    $trainer = User::factory()->create(['name' => 'Coach Mike']);

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    $mailable = new BookingSlotReminderEmail($bookingSlot);

    $mailable->assertSeeInHtml('Hey John');
});

test('reminder email contains session date and time', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    $mailable = new BookingSlotReminderEmail($bookingSlot);

    $mailable->assertSeeInHtml($tomorrow->format('l, F j'));
    $mailable->assertSeeInHtml($tomorrow->format('g:i A'));
});

test('reminder email contains trainer name', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create(['name' => 'Coach Mike']);

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    $mailable = new BookingSlotReminderEmail($bookingSlot);

    $mailable->assertSeeInHtml('Coach Mike');
});

test('reminder email has correct subject', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    $mailable = new BookingSlotReminderEmail($bookingSlot);

    expect($mailable->envelope()->subject)->toBe('Your training session is tomorrow!');
});

test('reminder email contains motivational message', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    $mailable = new BookingSlotReminderEmail($bookingSlot);

    expect($mailable->motivationalMessage)->toBeString();
    expect(strlen($mailable->motivationalMessage))->toBeGreaterThan(20);
});

test('reminder email contains the signature sign-off', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    $bookingSlot = BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    $mailable = new BookingSlotReminderEmail($bookingSlot);

    $mailable->assertSeeInHtml('Here to support you');
    $mailable->assertSeeInHtml('The Lift Station Team');
});

test('command logs reminder count when emails are sent', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->create();

    $booking = Booking::factory()
        ->for($member, 'member')
        ->for($trainer, 'trainer')
        ->active()
        ->create();

    $tomorrow = Date::tomorrow()->setHour(14)->setMinute(0);
    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow,
            'end_time' => $tomorrow->copy()->addHour(),
            'status' => Status::Upcoming,
        ]);

    BookingSlot::factory()
        ->for($booking)
        ->create([
            'start_time' => $tomorrow->copy()->addHour(),
            'end_time' => $tomorrow->copy()->addHours(2),
            'status' => Status::Upcoming,
        ]);

    Artisan::call(SendBookingSlotReminders::class);

    $output = Artisan::output();

    expect($output)->toContain('Total reminders sent: 2');
});

test('command logs when no reminders are sent', function () {
    Artisan::call(SendBookingSlotReminders::class);

    $output = Artisan::output();

    expect($output)->toContain('No reminder emails were sent');
});
