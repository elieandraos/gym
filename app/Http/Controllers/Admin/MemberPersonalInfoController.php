<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class MemberPersonalInfoController extends Controller
{
    public function index(User $user): Response
    {
        $user->load('memberActiveBooking');

        return Inertia::render('Admin/Members/PersonalInfo', [
            'member' => MemberResource::make($user),
        ]);
    }
}
