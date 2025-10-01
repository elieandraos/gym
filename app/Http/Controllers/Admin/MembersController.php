<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class MembersController extends Controller
{
    public function index(): Response
    {
        $members = User::query()
            ->members()
            ->when(request('search'), function (Builder $query, string $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->when(request()->has('activeTraining'), function (Builder $query) {
                if (request('activeTraining')) {
                    $query->whereHas('memberActiveBooking');
                } else {
                    $query->whereDoesntHave('memberActiveBooking');
                }
            })
            ->orderBy('registration_date', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Members/Index', [
            'members' => MemberResource::collection($members),
            'search' => request('search'),
            'activeTraining' => (bool) request('activeTraining', true),
        ]);
    }

    public function show(User $user): Response
    {
        $user->load([
            'memberActiveBooking.bookingSlots' => function ($query) {
                $query->orderBy('start_time')
                    ->with(['bookingSlotWorkouts.workout']);
            },
            'memberScheduledBookings',
        ]);

        return Inertia::render('Admin/Members/Show', [
            'member' => MemberResource::make($user),
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

        User::query()->create($request->all());

        return redirect()->route('admin.members.index')
            ->with('flash.banner', 'Member created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Members/Edit', [
            'member' => MemberResource::make($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()->route('admin.members.show', $user)
            ->with('flash.banner', 'Member updated successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
