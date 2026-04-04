<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateBooking;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\TrainerResource;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingsController extends Controller
{
    public function create(): Response
    {
        $renewFromBooking = null;
        $preSelectedMember = null;

        if (request()->has('renew_from')) {
            $renewFromBooking = Booking::query()
                ->with(['member', 'trainer', 'bookingSlots'])
                ->findOrFail(request('renew_from'));
        }

        if (request()->has('member_id')) {
            $preSelectedMember = User::query()
                ->members()
                ->findOrFail(request('member_id'));
        }

        return Inertia::render('Admin/Bookings/Create', [
            'trainers' => TrainerResource::collection(User::query()->trainers()->get()),
            'members' => MemberResource::collection(User::query()->members()->get()),
            'renewFromBooking' => $renewFromBooking ? BookingResource::make($renewFromBooking) : null,
            'preSelectedMember' => $preSelectedMember ? MemberResource::make($preSelectedMember) : null,
        ]);
    }

    public function store(BookingRequest $request, CreateBooking $createBooking): RedirectResponse
    {
        $booking = $createBooking->handle($request->all());

        return redirect(route('admin.members.show', [$booking->member_id]))
            ->with('flash.banner', 'Training created successfully')
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
