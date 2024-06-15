<?php
namespace App\Helpers;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use InvalidArgumentException;

function generateRepeatableDates(Carbon  $startDate, int $nb_dates, array $repeatableDayTime) : array
{
    $validDaysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    foreach ($repeatableDayTime as $day) {
        if (!in_array($day['day'], $validDaysOfWeek)) {
            throw new InvalidArgumentException('Invalid day in days array.');
        }

        try {
            Carbon::parse($day['time']);
        } catch (InvalidFormatException) {
            throw new InvalidArgumentException('Invalid time format in days array.');
        }
    }

    $sessionDates = [];
    $currentDate = $startDate->copy();

    while (count($sessionDates) < $nb_dates) {
        foreach ($repeatableDayTime as $day) {
            $dayOfWeek = Carbon::parse($day['day'])->dayOfWeek;
            $time = Carbon::parse($day['time'])->format('H:i');
            $nextSessionDate = $currentDate->copy()->next($dayOfWeek)->setTimeFromTimeString($time);

            if ($nextSessionDate >= $startDate && count($sessionDates) < $nb_dates) {
                $sessionDates[] = $nextSessionDate->format('Y-m-d h:i A');
                $currentDate = $nextSessionDate;
            }
        }
    }

    return $sessionDates;
}
