<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->byRole($request->query('role'))
            ->orderBy('registration_date', 'DESC')
            ->paginate(7)
            ->appends(request()->only(['role']));

        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($users),
            'role' => $request->query('role'),
        ]);
    }

    public function show(User $user): Response
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => UserResource::make($user),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'role' => in_array($request->query('role'), [Role::Member->value, Role::Trainer->value]) ? $request->query('role') : Role::Member->value,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $request->merge(['password' => Hash::make('password')]);
        User::create($request->all());

        return redirect(route('admin.users.index', ['role' => $request->input('role')]))
            ->with('flash.banner', $request->input('role').' created successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
