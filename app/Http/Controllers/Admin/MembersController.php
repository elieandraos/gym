<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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
            ->orderBy('registration_date', 'DESC')
            ->paginate(10);

        return Inertia::render('Admin/Members/Index', [
            'members' => UserResource::collection($members)
        ]);
    }

    public function show(User $user): Response
    {
        $user->load([
            'memberBookings' => function ($query) {
                $query->active()->with([
                    'trainer',
                    'bookingSlots' => fn ($query) => $query->orderBy('start_time'),
                ]);
            },
        ]);

        return Inertia::render('Admin/Members/Show', [
            'member' => UserResource::make($user),
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
}
