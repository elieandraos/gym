<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateWorkout;
use App\Actions\Admin\DeleteWorkout;
use App\Actions\Admin\UpdateWorkout;
use App\Enums\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkoutRequest;
use App\Http\Resources\WorkoutResource;
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
            ->when(request('categories'), function (Builder $query, array $categories) {
                $query->where(function (Builder $q) use ($categories) {
                    foreach ($categories as $category) {
                        $q->orWhereJsonContains('categories', $category);
                    }
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Workouts/Index', [
            'workouts' => WorkoutResource::collection($workouts),
            'search' => request('search'),
            'selectedCategories' => request('categories', []),
            'categories' => Category::values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Workouts/Create', [
            'categories' => Category::values(),
        ]);
    }

    public function store(WorkoutRequest $request, CreateWorkout $createWorkout): RedirectResponse
    {
        $createWorkout->handle($request->validated());

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

    public function update(WorkoutRequest $request, Workout $workout, UpdateWorkout $updateWorkout): RedirectResponse
    {
        $updateWorkout->handle($workout, $request->validated());

        return redirect()->route('admin.workouts.index')
            ->with('flash.banner', 'Workout updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function destroy(Workout $workout, DeleteWorkout $deleteWorkout): RedirectResponse
    {
        $deleteWorkout->handle($workout);

        return redirect()->route('admin.workouts.index')
            ->with('flash.banner', 'Workout deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
