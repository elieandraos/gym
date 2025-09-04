<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\TrainerResource;
use App\Models\User;
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
            ->orderBy('registration_date', 'DESC')
            ->paginate(10);

        return Inertia::render('Admin/Trainers/Index', [
            'trainers' => TrainerResource::collection($trainers),
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

        User::query()->create($request->all());

        return redirect()->route('admin.trainers.index')
            ->with('flash.banner', 'Trainer created successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
