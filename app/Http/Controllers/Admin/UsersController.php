<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');

        $users = User::query()
            ->when($role == 'Member', function ($query) {
                return $query->members();
            }, function ($query) use ($role) {
                return $role == 'Trainer' ? $query->trainers() : $query;
            })
            ->paginate();

        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($users)
        ]);
    }

    public function show(User $user) {
        return Inertia::render('Admin/Users/Show', [
            'user' => UserResource::make($user)
        ]);
    }
}
