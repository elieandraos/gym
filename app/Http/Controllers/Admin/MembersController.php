<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MemberResource;
use App\Mail\Member\WelcomeEmail;
use App\Mail\Owner\NewMemberEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
            $upcomingSlot = $slots->firstWhere('status', \App\Enums\Status::Upcoming);
            $completedCount = $slots->where('status', \App\Enums\Status::Complete)->count();
            $remainingCount = $user->memberActiveBooking->nb_sessions - $completedCount;

            $activeBookingData = array_merge(
                BookingResource::make($user->memberActiveBooking)->resolve(),
                [
                    'upcoming_session_url' => $upcomingSlot ? route('admin.bookings-slots.show', $upcomingSlot->id) : null,
                    'upcoming_session_date' => $upcomingSlot ? \Carbon\Carbon::parse($upcomingSlot->start_time)->isoFormat('ddd MMM Do') : null,
                    'upcoming_session_time' => $upcomingSlot ? \Carbon\Carbon::parse($upcomingSlot->start_time)->format('h:i A') : null,
                    'nb_remaining_sessions' => $remainingCount.' '.\Illuminate\Support\Str::plural('session', $remainingCount),
                ]
            );
        }

        return Inertia::render('Admin/Members/Show', [
            'member' => MemberResource::make($user),
            'activeBooking' => $activeBookingData,
            'scheduledBookings' => BookingResource::collection($user->memberScheduledBookings),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Members/Create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $request->merge([
            'password' => Hash::make('password'),
            'role' => Role::Member->value,
        ]);

        $member = User::query()->create($request->except(['photo', 'remove_photo']));

        if ($request->hasFile('photo')) {
            $member->updateProfilePhoto($request->file('photo'));
        }

        // Send email to the new member
        Mail::to($member->email)->queue(new WelcomeEmail($member));

        // Send email to gym owner(s) based on admin settings
        $admin = auth()->user();
        $sendEmailToOwners = $admin->getSetting('notifications.new_member_email_to_owners', true);

        if ($sendEmailToOwners) {
            // Get owner emails from admin settings, fallback to config
            $ownersEmails = $admin->getSetting('notifications.owner_emails', config('mail.owners_emails'));

            if ($ownersEmails) {
                $emails = array_map('trim', explode(',', $ownersEmails));
                foreach ($emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($email)->queue(new NewMemberEmail($member));
                    }
                }
            }
        }

        return redirect()->route('admin.member-created', $member);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Members/Edit', [
            'member' => MemberResource::make($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        if ($request->has('remove_photo') && $request->input('remove_photo') === true) {
            $user->deleteProfilePhoto();
        }

        $user->update($request->except(['photo', 'remove_photo']));

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

    public function destroy(User $user): RedirectResponse
    {
        // Delete storage files for body compositions and profile photos
        Storage::disk('public')->deleteDirectory("body-compositions/{$user->id}");
        Storage::disk('public')->deleteDirectory("profile-photos/{$user->id}");

        // Delete the user (CASCADE will handle bookings, booking_slots, booking_slot_workouts, and body_compositions)
        $user->delete();

        return redirect()->route('admin.members.index')
            ->with('flash.banner', 'Member and all associated data deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
