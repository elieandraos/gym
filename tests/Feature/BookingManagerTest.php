<?php

use App\Services\BookingManager;
use Carbon\Carbon;

it('generates dates forward correctly', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $scheduleDays = [
        ['day' => 'Monday', 'time' => '10:00 AM'],
        ['day' => 'Wednesday', 'time' => '02:00 PM'],
    ];

    $result = BookingManager::generateDatesForward($startDate, $nb_dates, $scheduleDays);

    expect($result)->toHaveCount($nb_dates);
    expect($result[0])->toBe('2023-06-05 10:00 AM');
    expect($result[1])->toBe('2023-06-07 02:00 PM');
    expect($result[2])->toBe('2023-06-12 10:00 AM');
    expect($result[3])->toBe('2023-06-14 02:00 PM');
    expect($result[4])->toBe('2023-06-19 10:00 AM');
});

it('generates dates backward correctly', function () {
    $endDate = Carbon::parse('2023-06-30 10:00');
    $count = 5;
    $scheduleDays = [
        ['day' => 'Monday', 'time' => '10:00 AM'],
        ['day' => 'Wednesday', 'time' => '02:00 PM'],
    ];

    $result = BookingManager::generateDatesBackward($endDate, $count, $scheduleDays);

    expect($result)->toHaveCount($count);
    // Results should be in chronological order (oldest first)
    expect($result[0])->toBe('2023-06-14 02:00 PM');
    expect($result[1])->toBe('2023-06-19 10:00 AM');
    expect($result[2])->toBe('2023-06-21 02:00 PM');
    expect($result[3])->toBe('2023-06-26 10:00 AM');
    expect($result[4])->toBe('2023-06-28 02:00 PM');
});

it('throws an exception for invalid day when generating dates', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $scheduleDays = [
        ['day' => 'Fun day', 'time' => '10:00 AM'],
    ];

    BookingManager::generateDatesForward($startDate, $nb_dates, $scheduleDays);
})->throws(InvalidArgumentException::class);

it('throws an exception for invalid time format when generating dates', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $scheduleDays = [
        ['day' => 'Monday', 'time' => '25:00 AM'],
    ];

    BookingManager::generateDatesForward($startDate, $nb_dates, $scheduleDays);
})->throws(InvalidArgumentException::class);

it('throws an exception for empty schedule days', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $scheduleDays = [];

    BookingManager::generateDatesForward($startDate, $nb_dates, $scheduleDays);
})->throws(InvalidArgumentException::class, 'Schedule days cannot be empty.');

it('throws an exception for invalid schedule structure', function () {
    $startDate = Carbon::parse('2023-06-01 10:00');
    $nb_dates = 5;
    $scheduleDays = [
        ['invalid' => 'structure'],
    ];

    BookingManager::generateDatesForward($startDate, $nb_dates, $scheduleDays);
})->throws(InvalidArgumentException::class);
