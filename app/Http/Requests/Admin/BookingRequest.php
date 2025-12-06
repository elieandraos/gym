<?php

namespace App\Http\Requests\Admin;

use App\Rules\DoesNotOverlapWithOtherMemberBookings;
use App\Rules\UniqueDaysInSchedule;
use App\Services\BookingManager;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nb_sessions' => ['required', 'integer'],
            'member_id' => ['required', 'exists:users,id'],
            'trainer_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date', new DoesNotOverlapWithOtherMemberBookings],
            'days' => ['required', 'array', new UniqueDaysInSchedule],
            'days.*.day' => ['required', 'string'],
            'days.*.time' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function passedValidation(): void
    {
        $startDate = Carbon::parse($this->input('start_date'));
        $nbSessions = $this->input('nb_sessions');
        $days = $this->input('days');

        $bookingSlotsDates = BookingManager::generateDatesForward($startDate, $nbSessions, $days);

        $this->merge([
            'booking_slots_dates' => $bookingSlotsDates,
            'end_date' => end($bookingSlotsDates),
            'schedule_days' => $days,
        ]);
    }

    public function messages(): array
    {
        return [
            'member_id.required' => 'The member field is required',
            'trainer_id.required' => 'The trainer field is required',
            'days.*.day.required' => 'Each day entry must have a day.',
            'days.*.time.required' => 'Each day entry must have a time.',
        ];
    }
}
