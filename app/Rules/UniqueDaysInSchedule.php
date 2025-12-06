<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueDaysInSchedule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Expect $value to be an array of ['day' => 'Monday', 'time' => '...']
        if (! is_array($value)) {
            return;
        }

        $days = array_map(fn ($item) => $item['day'] ?? null, $value);
        $days = array_filter($days); // Remove nulls

        // Check for duplicates
        $uniqueDays = array_unique($days);

        if (count($days) !== count($uniqueDays)) {
            // Find which day is duplicated
            $duplicates = array_diff_assoc($days, $uniqueDays);
            $duplicateDay = reset($duplicates);

            $fail("The day {$duplicateDay} appears more than once in the schedule.");
        }
    }
}
