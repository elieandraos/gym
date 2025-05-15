<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\BookingManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function index(string $role): Response
    {
        $users = User::query()
            ->byRole($role)
            ->orderBy('registration_date', 'DESC')
            ->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($users),
            'role' => $role,
        ]);
    }

    public function show(User $user, string $role): Response
    {
        $user = BookingManager::loadActiveBookingsWithSlotsForUser($user);
        //dd($user->toArray());
        return Inertia::render('Admin/Users/Show', [
            'user' => UserResource::make($user),
        ]);
    }

    public function create(string $role): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'role' => $role,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $request->merge(['password' => Hash::make('password')]);
        User::query()->create($request->all());

        return redirect(route('admin.users.index', $request->input('role')))
            ->with('flash.banner', $request->input('role').' created successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
