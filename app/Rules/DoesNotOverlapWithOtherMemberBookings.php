<?php

namespace App\Rules;

use App\Models\Booking;
use App\Services\BookingManager;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class DoesNotOverlapWithOtherMemberBookings implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // this validation rule requires these field to be filled
        if (! $this->data['member_id'] || ! $this->data['nb_sessions'] || empty($this->data['days'])) {
            return;
        }

        $startDate = Carbon::parse($value);

        $bookingSlotsDates = BookingManager::generateRepeatableDates($startDate, $this->data['nb_sessions'], $this->data['days']);
        $endDate = Carbon::parse(end($bookingSlotsDates));

        $overlappingBookings = Booking::query()->where('member_id', $this->data['member_id'])
            ->where(function ($query) use ($startDate, $endDate) {
                // Two bookings overlap if:
                // - New booking starts before existing ends AND new booking ends after existing starts
                // In SQL: existing.start_date < new.end_date AND existing.end_date > new.start_date
                $query->where('start_date', '<', $endDate)
                    ->where('end_date', '>', $startDate);
            })
            ->count();

        if ($overlappingBookings) {
            $fail('The training dates overlap with an existing training for the selected member.');
        }
    }
}
