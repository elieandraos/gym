<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');

        $users = User::query()
            ->byRole($role)
            ->orderBy('registration_date', 'DESC')
            ->paginate(5)
            ->appends(request()->only(['role']));

        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($users),
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => UserResource::make($user),
        ]);
    }

    public function store(UserRequest $request)
    {
        $request->merge(['password' => Hash::make('password'), 'role' => Role::Member->value]);
        User::create($request->all());

        return redirect(route('admin.users.index', ['role' => Role::Member->value]));
    }
}
