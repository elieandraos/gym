<?php

namespace App\Http\Controllers\Admin;

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
    /**
     * Display a paginated list of workouts with optional search and category filters.
     *
     * Builds a query that filters by a name search term (if provided) and by any of the
     * specified categories (matching JSON values in the `categories` column), orders results
     * by name, paginates at 10 items per page while preserving query string parameters,
     * and renders the Admin/Workouts/Index Inertia view.
     *
     * @return Response The Inertia response for the Admin/Workouts/Index view containing:
     *                  - `workouts`: collection of WorkoutResource for the paginated results
     *                  - `search`: current search term
     *                  - `selectedCategories`: current categories filter (array)
     *                  - `categories`: list of all category values
     */
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
