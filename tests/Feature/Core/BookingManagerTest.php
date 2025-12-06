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

    expect($result)->toHaveCount($nb_dates)
        ->and($result[0])->toBe('2023-06-05 10:00 AM')
        ->and($result[1])->toBe('2023-06-07 02:00 PM')
        ->and($result[2])->toBe('2023-06-12 10:00 AM')
        ->and($result[3])->toBe('2023-06-14 02:00 PM')
        ->and($result[4])->toBe('2023-06-19 10:00 AM');
});

it('generates dates starting on a scheduled day', function () {
    $startDate = Carbon::parse('2023-06-05 10:00'); // Monday
    $nb_dates = 5;
    $scheduleDays = [
        ['day' => 'Monday', 'time' => '10:00 AM'],
        ['day' => 'Wednesday', 'time' => '02:00 PM'],
        ['day' => 'Friday', 'time' => '03:00 PM'],
    ];

    $result = BookingManager::generateDatesForward($startDate, $nb_dates, $scheduleDays);

    expect($result)->toHaveCount($nb_dates)
        ->and($result[0])->toBe('2023-06-05 10:00 AM')
        ->and($result[1])->toBe('2023-06-07 02:00 PM')
        ->and($result[2])->toBe('2023-06-09 03:00 PM')
        ->and($result[3])->toBe('2023-06-12 10:00 AM')
        ->and($result[4])->toBe('2023-06-14 02:00 PM');
    // Should start on the same day (Monday)
    // Wednesday
    // Friday
    // Next Monday
    // Next Wednesday
});

it('generates dates backward correctly', function () {
    $endDate = Carbon::parse('2023-06-30 10:00');
    $count = 5;
    $scheduleDays = [
        ['day' => 'Monday', 'time' => '10:00 AM'],
        ['day' => 'Wednesday', 'time' => '02:00 PM'],
    ];

    $result = BookingManager::generateDatesBackward($endDate, $count, $scheduleDays);

    expect($result)->toHaveCount($count)
        ->and($result[0])->toBe('2023-06-14 02:00 PM')
        ->and($result[1])->toBe('2023-06-19 10:00 AM')
        ->and($result[2])->toBe('2023-06-21 02:00 PM')
        ->and($result[3])->toBe('2023-06-26 10:00 AM')
        ->and($result[4])->toBe('2023-06-28 02:00 PM');
    // Results should be in chronological order (oldest first)
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

describe('getNextAvailableDate', function () {
    it('returns next Monday for MWF pattern when after date is Friday', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '10:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:00 PM'],
            ['day' => 'Friday', 'time' => '10:00 AM'],
        ];

        $afterDate = Carbon::parse('2025-07-11'); // Friday
        $nextDate = BookingManager::getNextAvailableDate($afterDate, $scheduleDays);

        expect($nextDate)->toBe('2025-07-14'); // Next Monday
    });

    it('returns next Friday for MWF pattern when after date is Thursday', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '10:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:00 PM'],
            ['day' => 'Friday', 'time' => '10:00 AM'],
        ];

        $afterDate = Carbon::parse('2025-07-10'); // Thursday
        $nextDate = BookingManager::getNextAvailableDate($afterDate, $scheduleDays);

        expect($nextDate)->toBe('2025-07-11'); // Next Friday (earliest)
    });

    it('returns next occurrence for single day pattern', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '10:00 AM'],
        ];

        $afterDate = Carbon::parse('2025-07-10'); // Thursday
        $nextDate = BookingManager::getNextAvailableDate($afterDate, $scheduleDays);

        expect($nextDate)->toBe('2025-07-14'); // Next Monday
    });

    it('returns null when schedule days is empty', function () {
        $nextDate = BookingManager::getNextAvailableDate(Carbon::now(), []);

        expect($nextDate)->toBeNull();
    });

    it('works with distant past dates', function () {
        $scheduleDays = [
            ['day' => 'Tuesday', 'time' => '10:00 AM'],
            ['day' => 'Thursday', 'time' => '02:00 PM'],
        ];

        $afterDate = Carbon::parse('2020-01-06'); // Monday
        $nextDate = BookingManager::getNextAvailableDate($afterDate, $scheduleDays);

        expect($nextDate)->toBe('2020-01-07'); // Tuesday
    });

    it('returns next Monday when after date is Monday with MWF pattern', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '10:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:00 PM'],
            ['day' => 'Friday', 'time' => '10:00 AM'],
        ];

        $afterDate = Carbon::parse('2025-07-14'); // Monday
        $nextDate = BookingManager::getNextAvailableDate($afterDate, $scheduleDays);

        expect($nextDate)->toBe('2025-07-16'); // Next Wednesday (earliest after Monday)
    });
});

describe('getNextAvailableDateTime', function () {
    it('returns next date and time for MWF pattern when after date is Friday', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '10:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:00 PM'],
            ['day' => 'Friday', 'time' => '03:00 PM'],
        ];

        $afterDate = Carbon::parse('2025-07-11'); // Friday
        $result = BookingManager::getNextAvailableDateTime($afterDate, $scheduleDays);

        expect($result)->toBe([
            'date' => '2025-07-14',
            'time' => '10:00 AM',
        ]);
    });

    it('returns next date and time for MWF pattern when after date is Thursday', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '10:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:00 PM'],
            ['day' => 'Friday', 'time' => '03:00 PM'],
        ];

        $afterDate = Carbon::parse('2025-07-10'); // Thursday
        $result = BookingManager::getNextAvailableDateTime($afterDate, $scheduleDays);

        expect($result)->toBe([
            'date' => '2025-07-11',
            'time' => '03:00 PM',
        ]);
    });

    it('returns correct time for each day in pattern', function () {
        $scheduleDays = [
            ['day' => 'Monday', 'time' => '09:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:30 PM'],
            ['day' => 'Friday', 'time' => '04:00 PM'],
        ];

        $afterDate = Carbon::parse('2025-07-14'); // Monday
        $result = BookingManager::getNextAvailableDateTime($afterDate, $scheduleDays);

        expect($result)->toBe([
            'date' => '2025-07-16',
            'time' => '02:30 PM',
        ]);
    });

    it('returns null when schedule days is empty', function () {
        $result = BookingManager::getNextAvailableDateTime(Carbon::now(), []);

        expect($result)->toBeNull();
    });
});
