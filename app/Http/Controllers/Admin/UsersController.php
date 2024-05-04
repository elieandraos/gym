<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::query()->members()->latest('id')->paginate();

        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($users),
            'user' => UserResource::make(User::first()),
        ]);
    }
}
