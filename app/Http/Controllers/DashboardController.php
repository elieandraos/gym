<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\Status;
use App\Models\Booking;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        // Unpaid bookings count
        $unpaidBookings = Booking::query()
            ->where('is_paid', false)
            ->count();

        // Frozen bookings count
        $frozenBookings = Booking::query()
            ->where('is_frozen', true)
            ->count();

        // Bookings about to expire (2 sessions remaining)
        $expiringBookings = Booking::query()
            ->whereHas('bookingSlots', function ($q) {
                $q->where('status', Status::Upcoming->value);
            }, '=', 2)
            ->count();

        // Active members count
        $activeMembersCount = User::query()
            ->members()
            ->whereHas('memberActiveBooking')
            ->count();

        // Male/Female breakdown
        $maleCount = User::query()
            ->members()
            ->where('gender', Gender::Male->value)
            ->count();

        $femaleCount = User::query()
            ->members()
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
                    'profile_photo_url' => $trainer->profile_photo_url,
                    'active_members_count' => $trainer->active_members_count,
                ];
            });

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'unpaid_bookings' => $unpaidBookings,
                'frozen_bookings' => $frozenBookings,
                'expiring_bookings' => $expiringBookings,
                'active_members' => $activeMembersCount,
                'male_members' => $maleCount,
                'female_members' => $femaleCount,
            ],
            'trainers' => $trainers,
        ]);
    }
}
