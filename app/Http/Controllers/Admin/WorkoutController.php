<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkoutRequest;
use App\Http\Resources\Admin\WorkoutResource;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutController extends Controller
{
    public function index(): Response
    {
        $workouts = Workout::query()
            ->when(request('search'), function (Builder $query, string $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->when(request('category'), function (Builder $query, string $category) {
                $query->where('category', $category);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Workouts/Index', [
            'workouts' => WorkoutResource::collection($workouts),
            'search' => request('search'),
            'category' => request('category'),
            'categories' => Category::values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Workouts/Create', [
            'categories' => Category::values(),
        ]);
    }

    public function show(Workout $workout): Response
    {
        return Inertia::render('Admin/Workouts/Show', [
            'workout' => new WorkoutResource($workout),
        ]);
    }

    public function store(WorkoutRequest $request): RedirectResponse
    {
        Workout::query()->create($request->validated());

        return redirect()->route('admin.workouts.index')
            ->with('flash.banner', 'Workout created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function edit(Workout $workout): Response
    {
        return Inertia::render('Admin/Workouts/Edit', [
            'workout' => new WorkoutResource($workout),
            'categories' => Category::values(),
        ]);
    }

    public function update(WorkoutRequest $request, Workout $workout): RedirectResponse
    {
        $workout->update($request->validated());

        return redirect()->route('admin.workouts.index')
            ->with('flash.banner', 'Workout updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function destroy(Workout $workout): RedirectResponse
    {
        $workout->delete();

        return redirect()->route('admin.workouts.index')
            ->with('flash.banner', 'Workout deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
