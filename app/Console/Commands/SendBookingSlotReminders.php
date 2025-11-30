<?php

namespace App\Console\Commands;

use App\Enums\Status;
use App\Mail\Member\BookingSlotReminderEmail;
use App\Models\BookingSlot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

class SendBookingSlotReminders extends Command
{
    protected $signature = 'lift-station:send-booking-slot-reminders';

    protected $description = 'Send reminder emails to members for their upcoming training sessions tomorrow';

    public function handle(): void
    {
        $tomorrow = Date::tomorrow();

        $bookingSlots = BookingSlot::query()
            ->with(['booking.member', 'booking.trainer'])
            ->whereDate('start_time', '=', $tomorrow)
            ->where('status', Status::Upcoming)
            ->get();

        if ($bookingSlots->isEmpty()) {
            $this->info('No reminder emails were sent');

            return;
        }

        foreach ($bookingSlots as $bookingSlot) {
            Mail::to($bookingSlot->booking->member->email)
                ->queue(new BookingSlotReminderEmail($bookingSlot));

            $memberName = $bookingSlot->booking->member->name;
            $dateTime = $bookingSlot->start_time->format('Y-m-d H:i');

            $this->info("Reminder sent to $memberName for session on $dateTime");
        }

        $this->info("Total reminders sent: {$bookingSlots->count()}");
    }
}
