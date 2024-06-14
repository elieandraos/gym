<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingsController extends Controller
{

    public function create()
    {
        $trainers = UserResource::collection(User::query()->trainers()->get());
        $members = UserResource::collection(User::query()->members()->get());

        return Inertia::render('Admin/Bookings/Create', [
            'trainers' => $trainers,
            'members' => $members,
        ]);
    }

    public function store(BookingRequest $request) : RedirectResponse
    {
        $request->merge(['end_date' => Carbon::parse($request->start_date)->clone()->addDays(60)->format('Y-m-d') ]);
        $booking = Booking::create($request->all());

        return redirect(route('admin.users.show', [$booking->member_id, $booking->member->role]))
            ->with('flash.banner', 'Booking created successfully')
            ->with('flash.bannerStyle', 'success');
    }


    public function show(Booking $booking) : Response
    {
        $booking->load(['member', 'trainer', 'bookingSlots' => function ($query) {
            $query->orderBy('start_time');
        }]);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => BookingResource::make($booking)
        ]);
    }

    public function destroy(string $id)
    {
        //
    }
}
