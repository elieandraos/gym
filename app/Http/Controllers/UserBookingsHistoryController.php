<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserBookingsHistoryController extends Controller
{
    public function __invoke(User $user, Request $request): Response
    {
        $user->load(['memberBookings' => function ($query) {
            $query->with('trainer')->history();
        }]);

        return Inertia::render('Admin/UserBookingsHistory/Index', [
            'user' => UserResource::make($user),
        ]);
    }
}
