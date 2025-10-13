<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\Status;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        // Unpaid bookings with member and trainer info
        $unpaidBookings = Booking::query()
            ->where('is_paid', false)
            ->active()
            ->with(['member', 'trainer'])
            ->get();

        // Frozen bookings with member and trainer info
        $frozenBookings = Booking::query()
            ->where('is_frozen', true)
            ->with(['member', 'trainer'])
            ->get();

        // Bookings about to expire (2 sessions remaining)
        // Exclude members who already have a scheduled booking (likely renewed)
        $expiringBookings = Booking::query()
            ->active()
            ->with(['member', 'trainer', 'bookingSlots'])
            ->get()
            ->filter(function ($booking) {
                $upcomingCount = $booking->bookingSlots
                    ->where('status', Status::Upcoming->value)
                    ->count();

                if ($upcomingCount !== 2) {
                    return false;
                }

                // Check if member has a scheduled booking (starts in the future)
                $hasScheduledBooking = Booking::query()
                    ->where('member_id', $booking->member_id)
                    ->where('start_date', '>', now())
                    ->exists();

                return !$hasScheduledBooking;
            })
            ->values();

        // Active members count and average age (today)
        $activeMembers = User::query()
            ->members()
            ->whereHas('memberActiveBooking')
            ->get();

        $activeMembersCount = $activeMembers->count();
        $averageAge = $activeMembers->avg('age');

        // Active members count 30 days ago
        $thirtyDaysAgo = now()->subDays(30);
        $activeMembersCount30DaysAgo = User::query()
            ->members()
            ->whereHas('memberBookings', function ($query) use ($thirtyDaysAgo) {
                $query->where('start_date', '<=', $thirtyDaysAgo)
                    ->where('end_date', '>=', $thirtyDaysAgo);
            })
            ->count();

        $activeMembersChange = $activeMembersCount - $activeMembersCount30DaysAgo;

        // Male/Female breakdown of active members
        $maleCount = User::query()
            ->members()
            ->whereHas('memberActiveBooking')
            ->where('gender', Gender::Male->value)
            ->count();

        $femaleCount = User::query()
            ->members()
            ->whereHas('memberActiveBooking')
            ->where('gender', Gender::Female->value)
            ->count();

        // Trainers with active member counts
        $trainers = User::query()
            ->trainers()
            ->withCount(['trainerActiveBookings as active_members_count'])
            ->get()
            ->map(function ($trainer) {
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'color' => $trainer->color,
                    'active_members_count' => $trainer->active_members_count,
                    'profile_photo_url' => $trainer->profile_photo_url,
                ];
            });

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'active_members' => $activeMembersCount,
                'active_members_change' => $activeMembersChange,
                'average_age' => round($averageAge),
                'male_members' => $maleCount,
                'female_members' => $femaleCount,
            ],
            'bookings' => [
                'unpaid' => BookingResource::collection($unpaidBookings),
                'frozen' => BookingResource::collection($frozenBookings),
                'expiring' => BookingResource::collection($expiringBookings),
            ],
            'trainers' => $trainers,
        ]);
    }
}
