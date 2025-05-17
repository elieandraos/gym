<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Bookings/Create', [
            'bookings' => [],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Bookings/Create', [
            'trainers' => UserResource::collection(User::query()->trainers()->get()),
            'members' => UserResource::collection(User::query()->members()->get()),
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $bookingSlots = [];

        // calculate booking slots dates
        foreach ($request->input('booking_slots_dates') as $date) {
            $startDate = Carbon::parse($date);
            $bookingSlots[] = new BookingSlot([
                'start_time' => $startDate,
                'end_time' => $startDate->clone()->addHour(),
                'status' => $startDate < Carbon::now() ? Status::Complete->value : Status::Upcoming->value,
            ]);
        }

        // set the booking end_date to the last session date
        $request->merge(['end_date' => end($bookingSlots)->start_time->toDateString()]);

        // create the booking and its slots
        $booking = Booking::query()->create($request->all());
        $booking->bookingSlots()->saveMany($bookingSlots);

        return redirect(route('admin.members.show', [$booking->member_id]))
            ->with('flash.banner', 'Booking created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function show(Booking $booking): Response
    {
        $booking->load(['member', 'trainer', 'bookingSlots' => function ($query) {
            $query->orderBy('start_time');
        }]);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => BookingResource::make($booking),
        ]);
    }
}
