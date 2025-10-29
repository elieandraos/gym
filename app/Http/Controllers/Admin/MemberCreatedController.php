<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class MemberCreatedController extends Controller
{
    public function __invoke(User $user): Response
    {
        return Inertia::render('Admin/MemberCreated/Index', [
            'member' => MemberResource::make($user),
        ]);
    }
}
