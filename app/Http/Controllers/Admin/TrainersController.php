<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\TrainerResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class TrainersController extends Controller
{
    public function index(): Response
    {
        $trainers = User::query()
            ->trainers()
            ->when(request('search'), function (Builder $query, string $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('registration_date', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Trainers/Index', [
            'trainers' => TrainerResource::collection($trainers),
            'search' => request('search'),
        ]);
    }

    public function show(User $user): Response
    {
        return Inertia::render('Admin/Trainers/Show', [
            'trainer' => TrainerResource::make($user),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Trainers/Create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $request->merge([
            'password' => Hash::make('password'),
            'role' => Role::Trainer->value,
        ]);

        $trainer = User::query()->create($request->except(['photo', 'remove_photo']));

        if ($request->hasFile('photo')) {
            $trainer->updateProfilePhoto($request->file('photo'));
        }

        return redirect()->route('admin.trainers.index')
            ->with('flash.banner', 'Trainer created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Trainers/Edit', [
            'trainer' => TrainerResource::make($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        if ($request->has('remove_photo') && $request->input('remove_photo') === true) {
            $user->deleteProfilePhoto();
        }

        $user->update($request->except(['photo', 'remove_photo']));

        return redirect()->route('admin.trainers.show', $user)
            ->with('flash.banner', 'Trainer updated successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
