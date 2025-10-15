<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use InvalidArgumentException;

class BookingManager
{
    /**
     * Validates schedule_days structure
     * Expected format: [['day' => 'Monday', 'time' => '10:00 am'], ...]
     */
    private static function validateScheduleDays(array $scheduleDays): void
    {
        if (empty($scheduleDays)) {
            throw new InvalidArgumentException('Schedule days cannot be empty.');
        }

        $validDaysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($scheduleDays as $index => $dayTime) {
            // Validate structure
            if (! is_array($dayTime) || ! isset($dayTime['day']) || ! isset($dayTime['time'])) {
                throw new InvalidArgumentException("Invalid structure at index $index. Expected ['day' => 'Day', 'time' => 'HH:MM AM/PM'].");
            }

            // Validate day
            if (! in_array($dayTime['day'], $validDaysOfWeek)) {
                throw new InvalidArgumentException("Invalid day '{$dayTime['day']}' at index $index. Must be one of: ".implode(', ', $validDaysOfWeek));
            }

            // Validate time format
            try {
                Carbon::parse($dayTime['time']);
            } catch (InvalidFormatException) {
                throw new InvalidArgumentException("Invalid time format '{$dayTime['time']}' at index $index.");
            }
        }
    }

    /**
     * Generate dates forward from a start date following a schedule pattern
     */
    public static function generateDatesForward(Carbon $startDate, int $count, array $scheduleDays): array
    {
        self::validateScheduleDays($scheduleDays);

        $sessionDates = [];
        $currentDate = $startDate->copy();

        while (count($sessionDates) < $count) {
            foreach ($scheduleDays as $dayTime) {
                $dayOfWeek = Carbon::parse($dayTime['day'])->dayOfWeek;
                $time = Carbon::parse($dayTime['time'])->format('H:i');
                $nextDate = $currentDate->copy()->next($dayOfWeek)->setTimeFromTimeString($time);

                if ($nextDate >= $startDate && count($sessionDates) < $count) {
                    $sessionDates[] = $nextDate->format('Y-m-d h:i A');
                    $currentDate = $nextDate;
                }
            }
        }

        return $sessionDates;
    }

    /**
     * Generate dates backward from an end date following a schedule pattern
     * Returns dates in chronological order (oldest first)
     */
    public static function generateDatesBackward(Carbon $endDate, int $count, array $scheduleDays): array
    {
        self::validateScheduleDays($scheduleDays);

        $sessionDates = [];
        $currentDate = $endDate->copy();

        while (count($sessionDates) < $count) {
            foreach (array_reverse($scheduleDays) as $dayTime) {
                $dayOfWeek = Carbon::parse($dayTime['day'])->dayOfWeek;
                $time = Carbon::parse($dayTime['time'])->format('H:i');
                $previousDate = $currentDate->copy()->previous($dayOfWeek)->setTimeFromTimeString($time);

                if ($previousDate <= $endDate && count($sessionDates) < $count) {
                    $sessionDates[] = $previousDate->format('Y-m-d h:i A');
                    $currentDate = $previousDate;
                }
            }
        }

        return array_reverse($sessionDates); // Return chronological order
    }
}
