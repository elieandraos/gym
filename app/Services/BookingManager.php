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
        $weekStart = $startDate->copy()->startOfWeek();
        $currentWeekOffset = 0;

        // Extract day numbers and times (using ISO day numbering: Monday=1, Sunday=7)
        $schedule = array_map(function ($dayTime) {
            return [
                'dayOfWeek' => Carbon::parse($dayTime['day'])->dayOfWeekIso,
                'time' => Carbon::parse($dayTime['time'])->format('H:i'),
            ];
        }, $scheduleDays);

        // Sort by day of week to maintain chronological order
        usort($schedule, fn ($a, $b) => $a['dayOfWeek'] <=> $b['dayOfWeek']);

        while (count($sessionDates) < $count) {
            foreach ($schedule as $slot) {
                $candidateDate = $weekStart->copy()
                    ->addWeeks($currentWeekOffset)
                    ->startOfWeek()
                    ->addDays($slot['dayOfWeek'] - 1)
                    ->setTimeFromTimeString($slot['time']);

                if ($candidateDate >= $startDate && count($sessionDates) < $count) {
                    $sessionDates[] = $candidateDate->format('Y-m-d h:i A');
                }
            }
            $currentWeekOffset++;
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

    /**
     * Get the next available date after a given date following the schedule pattern
     * Returns only the date (not time) of the earliest next occurrence
     */
    public static function getNextAvailableDate(Carbon $afterDate, array $scheduleDays): ?string
    {
        $result = self::getNextAvailableDateTime($afterDate, $scheduleDays);

        return $result ? $result['date'] : null;
    }

    /**
     * Get the next available date and time after a given date following the schedule pattern
     * Returns array with 'date' and 'time' of the earliest next occurrence
     */
    public static function getNextAvailableDateTime(Carbon $afterDate, array $scheduleDays): ?array
    {
        if (empty($scheduleDays)) {
            return null;
        }

        // Create a map of day number to time
        $scheduleDayMap = [];
        foreach ($scheduleDays as $dayTime) {
            $dayNumber = Carbon::parse($dayTime['day'])->dayOfWeekIso;
            // Convert time to uppercase AM/PM format for consistency
            $time = str_replace(['am', 'pm'], ['AM', 'PM'], $dayTime['time']);
            $scheduleDayMap[$dayNumber] = $time;
        }

        // Start searching from the day after the given date
        $searchDate = $afterDate->copy()->addDay();

        // Search through the next 7 days for the first match
        for ($i = 0; $i < 7; $i++) {
            $currentDayOfWeek = $searchDate->dayOfWeekIso;

            if (isset($scheduleDayMap[$currentDayOfWeek])) {
                return [
                    'date' => $searchDate->format('Y-m-d'),
                    'time' => $scheduleDayMap[$currentDayOfWeek],
                ];
            }

            $searchDate->addDay();
        }

        return null;
    }
}
