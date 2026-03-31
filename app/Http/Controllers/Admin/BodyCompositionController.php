<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateBodyComposition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BodyCompositionRequest;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BodyCompositionController extends Controller
{
    public function create(User $user): Response
    {
        $user->load('memberActiveBooking');

        return Inertia::render('Admin/BodyCompositions/Create', [
            'member' => MemberResource::make($user),
            'defaultDate' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    public function store(
        BodyCompositionRequest $request,
        User $user,
        CreateBodyComposition $createBodyComposition,
    ): RedirectResponse {
        $createBodyComposition->handle($user, $request->validated());

        return redirect()->route('admin.members.show', $user)
            ->with('flash.banner', 'Body composition photo uploaded successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
