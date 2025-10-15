<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Services\BookingManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    // Configuration constants
    private const EXPIRING_BOOKING_SESSIONS = 12;
    private const EXPIRING_BOOKING_COUNT = 3;
    private const COMPLETED_SESSIONS_TARGET = 10; // For expiring bookings
    private const UPCOMING_SESSIONS_TARGET = 2; // For expiring bookings
    private const UNPAID_PERCENTAGE = 20;
    private const MIN_COMPLETED_BOOKING_MONTHS = 2;
    private const MAX_COMPLETED_BOOKING_MONTHS = 6;

    // Fixed schedule pattern for expiring bookings (ensures predictable timing)
    private const EXPIRING_BOOKING_SCHEDULE = [
        ['day' => 'Monday', 'time' => '10:00 am'],
        ['day' => 'Wednesday', 'time' => '10:00 am'],
        ['day' => 'Friday', 'time' => '10:00 am'],
    ];

    public function run(): void
    {
        $trainers = User::query()->trainers()->get();
        $members = User::query()->members()->get();

        $members->each(function ($user, $index) use ($trainers) {
            // Add completed bookings (history) - at least 2 months ago to avoid overlap
            $this->addCompletedBookings(
                $user,
                $trainers,
                nbMonthsAgo: fake()->numberBetween(self::MIN_COMPLETED_BOOKING_MONTHS, self::MAX_COMPLETED_BOOKING_MONTHS)
            );

            // First few members get soon-to-expire booking only (for testing renewals)
            if ($index < self::EXPIRING_BOOKING_COUNT) {
                $this->addSoonToExpireBooking($user, $trainers);
            } else {
                // Other members get regular active booking
                $this->addActiveBooking($user, $trainers);
            }
        });
    }

    protected function addActiveBooking(User $member, Collection $trainers): void
    {
        $factory = Booking::factory()->active();

        // Make some active bookings unpaid
        if (fake()->boolean(self::UNPAID_PERCENTAGE)) {
            $factory = $factory->unpaid();
        }

        /** @var Booking $booking */
        $booking = $factory->create([
            'member_id' => $member->id,
            'trainer_id' => $this->selectRandomTrainer($trainers)->id,
        ]);

        $this->createBookingSlotsForBooking($booking);
    }

    protected function addCompletedBookings(User $member, Collection $trainers, int $nbMonthsAgo = 3): void
    {
        if ($nbMonthsAgo <= 0) {
            return;
        }

        /** @var Booking $booking */
        $booking = Booking::factory()
            ->completed($nbMonthsAgo)
            ->create([
                'member_id' => $member->id,
                'trainer_id' => $this->selectRandomTrainer($trainers)->id,
            ]);

        $this->createBookingSlotsForBooking($booking);
    }

    protected function addSoonToExpireBooking(User $member, Collection $trainers): void
    {
        // Calculate the precise start date to ensure exactly N completed and M upcoming sessions
        $startDate = $this->calculateExpiringBookingStartDate(
            self::EXPIRING_BOOKING_SESSIONS,
            self::EXPIRING_BOOKING_SCHEDULE
        );

        // Create booking directly with all correct attributes (bypassing factory defaults)
        /** @var Booking $booking */
        $booking = Booking::query()->create([
            'member_id' => $member->id,
            'trainer_id' => $this->selectRandomTrainer($trainers)->id,
            'nb_sessions' => self::EXPIRING_BOOKING_SESSIONS,
            'start_date' => $startDate->toDateString(),
            'end_date' => $startDate->copy()->addDays(30)->toDateString(), // temporary, will update later
            'schedule_days' => self::EXPIRING_BOOKING_SCHEDULE,
            'is_paid' => true,
            'is_frozen' => false,
            'frozen_at' => null,
        ]);

        // Generate slot dates with calculated start date
        $slotDates = BookingManager::generateRepeatableDates(
            $startDate,
            self::EXPIRING_BOOKING_SESSIONS,
            self::EXPIRING_BOOKING_SCHEDULE
        );

        // Create slots with status based on actual dates (past = complete, future = upcoming)
        $this->createSlotsFromDates($booking, $slotDates);

        // Update booking end_date to the last slot's date
        $booking->updateEndDateToLastSlot();
    }

    protected function createBookingSlotsForBooking(Booking $booking): void
    {
        // Generate proper slot dates using BookingManager based on schedule_days
        $slotDates = BookingManager::generateRepeatableDates(
            Carbon::parse($booking->start_date),
            $booking->nb_sessions,
            $booking->schedule_days
        );

        // Create slots with proper dates and times from schedule
        $this->createSlotsFromDates($booking, $slotDates);

        $booking->updateEndDateToLastSlot();
    }

    /**
     * Helper: Select a random trainer from the collection
     */
    private function selectRandomTrainer(Collection $trainers): User
    {
        /** @var User */
        return $trainers->random();
    }

    /**
     * Helper: Calculate the start date for an expiring booking
     * Ensures exactly COMPLETED_SESSIONS_TARGET completed and UPCOMING_SESSIONS_TARGET upcoming sessions
     */
    private function calculateExpiringBookingStartDate(int $nbSessions, array $scheduleDays): Carbon
    {
        // Start from 12 weeks ago to generate initial slot dates
        $tempStartDate = Carbon::now()->subWeeks(12)->startOfDay();

        // Generate temporary slot dates to calculate the proper start date
        $tempSlotDates = BookingManager::generateRepeatableDates(
            $tempStartDate,
            $nbSessions,
            $scheduleDays
        );

        // Calculate adjusted start date so slot #(COMPLETED_SESSIONS_TARGET + 1) is 2 days in the future
        // This ensures exactly COMPLETED_SESSIONS_TARGET completed and UPCOMING_SESSIONS_TARGET upcoming sessions
        $targetSlotIndex = self::COMPLETED_SESSIONS_TARGET; // 0-indexed, so slot #11 is at index 10
        $currentSlotDate = Carbon::parse($tempSlotDates[$targetSlotIndex])->startOfDay();
        $targetSlotDate = Carbon::now()->addDays(self::UPCOMING_SESSIONS_TARGET)->startOfDay();

        // Calculate offset to shift all slots (using whole days only)
        $offsetDays = (int) $currentSlotDate->diffInDays($targetSlotDate, false);

        return $tempStartDate->copy()->addDays($offsetDays);
    }

    /**
     * Helper: Create booking slots from an array of date strings
     */
    private function createSlotsFromDates(Booking $booking, array $slotDates): void
    {
        foreach ($slotDates as $slotDate) {
            $startTime = Carbon::parse($slotDate);
            $endTime = $startTime->copy()->addHour();

            BookingSlot::query()->create([
                'booking_id' => $booking->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $startTime->isPast() ? Status::Complete : Status::Upcoming,
            ]);
        }
    }
}
