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
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->count();

        if ($overlappingBookings) {
            $fail('The training dates overlap with an existing training for the selected member.');
        }
    }
}
