<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateMember;
use App\Actions\Admin\DeleteMember;
use App\Actions\Admin\UpdateMember;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MembersController extends Controller
{
    public function index(): Response
    {
        $trainingStatus = request('trainingStatus', 'all');

        $members = User::query()
            ->members()
            ->when(request('search'), function (Builder $query, string $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->when($trainingStatus === 'active', function (Builder $query) {
                $query->whereHas('memberActiveBooking');
            })
            ->when($trainingStatus === 'dormant', function (Builder $query) {
                $query->whereDoesntHave('memberActiveBooking');
            })
            ->orderBy('registration_date', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Members/Index', [
            'members' => MemberResource::collection($members),
            'search' => request('search'),
            'trainingStatus' => $trainingStatus,
        ]);
    }

    public function show(User $user): Response
    {
        $user->load([
            'memberActiveBooking.trainer',
            'memberActiveBooking.bookingSlots' => function ($query) {
                $query->orderBy('start_time');
            },
            'memberScheduledBookings.trainer',
            'lastBodyComposition',
        ]);

        // Calculate stats for active booking
        $activeBookingData = null;
        if ($user->memberActiveBooking) {
            $slots = $user->memberActiveBooking->bookingSlots->sortBy('start_time')->values();
            $upcomingSlot = $slots->firstWhere('status', Status::Upcoming);
            $completedCount = $slots->where('status', Status::Complete)->count();
            $remainingCount = $user->memberActiveBooking->nb_sessions - $completedCount;

            $activeBookingData = array_merge(
                BookingResource::make($user->memberActiveBooking)->resolve(),
                [
                    'upcoming_session_url' => $upcomingSlot ? route('admin.bookings-slots.show', $upcomingSlot->id) : null,
                    'upcoming_session_date' => $upcomingSlot ? Carbon::parse($upcomingSlot->start_time)->isoFormat('ddd MMM Do') : null,
                    'upcoming_session_time' => $upcomingSlot ? Carbon::parse($upcomingSlot->start_time)->format('h:i A') : null,
                    'nb_remaining_sessions' => $remainingCount.' '.Str::plural('session', $remainingCount),
                ]
            );
        }

        $lastCompletedBooking = null;
        if (! $user->memberActiveBooking) {
            $lastCompletedBooking = $user->memberCompletedBookings()
                ->where('end_date', '>=', Carbon::today()->subWeeks(3))
                ->with('trainer')
                ->first();
        }

        return Inertia::render('Admin/Members/Show', [
            'member' => MemberResource::make($user),
            'activeBooking' => $activeBookingData,
            'scheduledBookings' => BookingResource::collection($user->memberScheduledBookings),
            'lastCompletedBooking' => $lastCompletedBooking ? BookingResource::make($lastCompletedBooking) : null,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Members/Create');
    }

    public function store(UserRequest $request, CreateMember $createMember): RedirectResponse
    {
        /** @var User $admin */
        $admin = auth()->user();
        $member = $createMember->handle($admin, $request->validated());

        return redirect()->route('admin.member-created', $member);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Members/Edit', [
            'member' => MemberResource::make($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateMember $updateMember): RedirectResponse
    {
        $updateMember->handle($user, $request->validated());

        return redirect()->route('admin.members.show', $user)
            ->with('flash.banner', 'Member updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function delete(User $user): Response
    {
        return Inertia::render('Admin/DeleteMember/Index', [
            'member' => MemberResource::make($user),
        ]);
    }

    public function destroy(User $user, DeleteMember $deleteMember): RedirectResponse
    {
        $deleteMember->handle($user);

        return redirect()->route('admin.members.index')
            ->with('flash.banner', 'Member and all associated data deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
