<?php
namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use InvalidArgumentException;

class BookingManager
{
    public static function loadActiveBookingsWithSlotsForUser(User $user): User
    {
        $bookingsRelation = match (strtolower($user->role)) {
            Role::Member->value => 'memberBookings',
            Role::Trainer->value => 'trainerBookings',
            default => throw new InvalidArgumentException('Invalid role provided.'),
        };

        $otherSideRelation = match (strtolower($user->role)) {
            Role::Member->value => 'trainer',
            Role::Trainer->value => 'member',
            default => throw new InvalidArgumentException('Invalid role provided.'),
        };

        return $user->load([
            $bookingsRelation => function ($query) use ($otherSideRelation) {
                $query->active()
                    ->with([
                        $otherSideRelation,
                        'bookingSlots' => function ($query) {
                            $query->orderBy('start_time', 'ASC');
                        },
                    ]);
            },
        ]);
    }

    public static function generateRepeatableDates(Carbon $startDate, int $nb_dates, array $repeatableDayTime): array
    {
        $validDaysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($repeatableDayTime as $day) {
            if (! in_array($day['day'], $validDaysOfWeek)) {
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
}
