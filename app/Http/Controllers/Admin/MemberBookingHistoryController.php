<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class MemberBookingHistoryController extends Controller
{
    public function index(User $user): Response
    {
        $user->load('memberActiveBooking');
        $completedBookings = $user->memberCompletedBookings()->with('trainer')->get();

        return Inertia::render('Admin/MemberBookingsHistory/Index', [
            'member' => MemberResource::make($user),
            'bookings' => BookingResource::collection($completedBookings),
        ]);
    }
}
