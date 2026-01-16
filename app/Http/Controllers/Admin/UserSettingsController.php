<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserSettingsRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserSettingsController extends Controller
{
    public function edit(): Response
    {
        $trainers = User::query()
            ->trainers()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($trainer) => [
                'value' => $trainer->id,
                'label' => $trainer->name,
            ]);

        /** @var User $user */
        $user = auth()->user();

        return Inertia::render('Admin/Settings/Index', [
            'trainers' => $trainers,
            'settings' => $user->getSetting(),
        ]);
    }

    public function update(UserSettingsRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $user->updateSettings($request->validated());

        return redirect()->route('admin.settings.edit')
            ->with('flash.banner', 'Settings updated successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
