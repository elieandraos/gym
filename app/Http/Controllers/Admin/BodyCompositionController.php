<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BodyCompositionRequest;
use App\Http\Resources\MemberResource;
use App\Models\BodyComposition;
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

    public function store(BodyCompositionRequest $request, User $user): RedirectResponse
    {
        $photoPath = $request->file('photo')->store('body-compositions', 'public');

        BodyComposition::query()->create([
            'user_id' => $user->id,
            'photo_path' => $photoPath,
            'taken_at' => $request->validated('taken_at'),
        ]);

        return redirect()->route('admin.members.show', $user)
            ->with('flash.banner', 'Body composition photo uploaded successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
