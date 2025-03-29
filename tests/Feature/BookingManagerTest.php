<?php

use App\Services\BookingManager;
use Carbon\Carbon;

it('generates repeatable dates correctly', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $repeatableDayTime = [
        ['day' => 'Monday', 'time' => '10:00 AM'],
        ['day' => 'Wednesday', 'time' => '02:00 PM'],
    ];

    $result = BookingManager::generateRepeatableDates($startDate, $nb_dates, $repeatableDayTime);

    expect($result)->toHaveCount($nb_dates);
    expect($result[0])->toBe('2023-06-05 10:00 AM');
    expect($result[1])->toBe('2023-06-07 02:00 PM');
    expect($result[2])->toBe('2023-06-12 10:00 AM');
    expect($result[3])->toBe('2023-06-14 02:00 PM');
    expect($result[4])->toBe('2023-06-19 10:00 AM');
});

it('throws an exception for invalid day', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $repeatableDayTime = [
        ['day' => 'Fun day', 'time' => '10:00 AM'],
    ];

    BookingManager::generateRepeatableDates($startDate, $nb_dates, $repeatableDayTime);
})->throws(InvalidArgumentException::class, 'Invalid day in days array.');

it('throws an exception for invalid time format', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $repeatableDayTime = [
        ['day' => 'Monday', 'time' => '25:00 AM'],
    ];

    BookingManager::generateRepeatableDates($startDate, $nb_dates, $repeatableDayTime);
})->throws(InvalidArgumentException::class, 'Invalid time format in days array.');
