# Implementation Plan: Workout Circuits Feature (Simplified)

## Overview

This plan introduces **workout circuits** to the gym management system, replacing category-based grouping with circuit-based organization. A circuit groups multiple workouts that are performed together within a training session.

**SIMPLIFIED APPROACH:** Since production data will be truncated, we use a single migration with full table renaming for perfect naming consistency.

### Key Changes
- Create new `booking_slot_circuits` table
- **Rename** `booking_slot_workouts` → `booking_slot_circuit_workouts`
- **Rename** `booking_slot_workout_sets` → `booking_slot_circuit_workout_sets`
- **Rename FK column** `booking_slot_workout_id` → `booking_slot_circuit_workout_id`
- Remove `booking_slot_id` from workouts table (clean hierarchy)
- Add `booking_slot_circuit_id` to workouts table
- Rename models: `BookingSlotCircuitWorkout` and `BookingSlotCircuitWorkoutSet`
- **Perfect naming consistency** - no `$table` properties needed!
- **Clean relationship names** - `circuits.circuitWorkouts.workout`
- **NO position/ordering fields** - order doesn't matter

### User Requirements Met
✅ Multiple circuits per session (e.g., Circuit 1: upper body, Circuit 2: core)
✅ Circuits group workouts together
✅ Individual workout sets preserved (reps, weight, duration)
✅ Remove category-based grouping in favor of circuits
✅ Clean hierarchy: BookingSlot → Circuit → CircuitWorkout → Sets
✅ Perfect naming consistency throughout database and models
✅ Semantic relationship names for clarity
✅ Simplified migration - no data preservation needed

---

## 1. Database Migration (Single Migration)

### Migration: Create Circuits and Rename Tables
**File:** `database/migrations/2025_12_27_000001_create_circuits_and_update_workouts.php`

**IMPORTANT: Truncate before running migration:**
```bash
# In production, truncate the tables first:
mysql -u your_user -p your_database
> TRUNCATE TABLE booking_slot_workout_sets;
> TRUNCATE TABLE booking_slot_workouts;
> exit;

# Then run migration:
php artisan migrate
```

**Migration code:**
```php
<?php

use App\Models\BookingSlot;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create circuits table
        Schema::create('booking_slot_circuits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BookingSlot::class)->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        // Rename tables for consistency
        Schema::rename('booking_slot_workouts', 'booking_slot_circuit_workouts');
        Schema::rename('booking_slot_workout_sets', 'booking_slot_circuit_workout_sets');

        // Update booking_slot_circuit_workouts table
        Schema::table('booking_slot_circuit_workouts', function (Blueprint $table) {
            // Drop old foreign key and column
            $table->dropForeign(['booking_slot_id']);
            $table->dropColumn('booking_slot_id');

            // Add new foreign key to circuits
            $table->foreignId('booking_slot_circuit_id')
                ->after('id')
                ->constrained('booking_slot_circuits')
                ->onDelete('cascade');
        });

        // Update booking_slot_circuit_workout_sets table - rename FK column
        Schema::table('booking_slot_circuit_workout_sets', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['booking_slot_workout_id']);

            // Rename the column for consistency
            $table->renameColumn('booking_slot_workout_id', 'booking_slot_circuit_workout_id');

            // Add foreign key back with new name
            $table->foreign('booking_slot_circuit_workout_id')
                ->references('id')
                ->on('booking_slot_circuit_workouts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Reverse FK column rename in sets table
        Schema::table('booking_slot_circuit_workout_sets', function (Blueprint $table) {
            $table->dropForeign(['booking_slot_circuit_workout_id']);
            $table->renameColumn('booking_slot_circuit_workout_id', 'booking_slot_workout_id');
            $table->foreign('booking_slot_workout_id')
                ->references('id')
                ->on('booking_slot_circuit_workouts')
                ->onDelete('cascade');
        });

        // Reverse workouts table changes
        Schema::table('booking_slot_circuit_workouts', function (Blueprint $table) {
            $table->dropForeign(['booking_slot_circuit_id']);
            $table->dropColumn('booking_slot_circuit_id');

            $table->foreignId('booking_slot_id')
                ->after('id')
                ->constrained('booking_slots')
                ->onDelete('cascade');
        });

        // Rename tables back
        Schema::rename('booking_slot_circuit_workout_sets', 'booking_slot_workout_sets');
        Schema::rename('booking_slot_circuit_workouts', 'booking_slot_workouts');

        // Drop circuits table
        Schema::dropIfExists('booking_slot_circuits');
    }
};
```

---

## 2. Model Updates

### New Model: BookingSlotCircuit
**File:** `app/Models/BookingSlotCircuit.php` (create new)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSlotCircuit extends Model
{
    use HasFactory;

    protected $fillable = ['booking_slot_id', 'name'];

    public function bookingSlot(): BelongsTo
    {
        return $this->belongsTo(BookingSlot::class);
    }

    public function circuitWorkouts(): HasMany
    {
        return $this->hasMany(BookingSlotCircuitWorkout::class);
    }
}
```

### Update BookingSlot Model
**File:** `app/Models/BookingSlot.php`

Add new relationship:
```php
public function circuits(): HasMany
{
    return $this->hasMany(BookingSlotCircuit::class);
}
```

### Rename: BookingSlotWorkout → BookingSlotCircuitWorkout
**File:** Rename `app/Models/BookingSlotWorkout.php` → `app/Models/BookingSlotCircuitWorkout.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSlotCircuitWorkout extends Model
{
    use HasFactory;

    // ✅ No $table property needed - perfect naming match!

    protected $fillable = [
        'booking_slot_circuit_id',
        'workout_id',
    ];

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(BookingSlotCircuit::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(BookingSlotCircuitWorkoutSet::class);
    }
}
```

### Rename: BookingSlotWorkoutSet → BookingSlotCircuitWorkoutSet
**File:** Rename `app/Models/BookingSlotWorkoutSet.php` → `app/Models/BookingSlotCircuitWorkoutSet.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSlotCircuitWorkoutSet extends Model
{
    use HasFactory;

    // ✅ No $table property needed - perfect naming match!

    protected $fillable = [
        'booking_slot_circuit_workout_id',
        'reps',
        'weight_in_kg',
        'duration_in_seconds',
    ];

    protected $casts = [
        'reps' => 'integer',
        'weight_in_kg' => 'decimal:2',
        'duration_in_seconds' => 'integer',
    ];

    public function circuitWorkout(): BelongsTo
    {
        return $this->belongsTo(BookingSlotCircuitWorkout::class);
    }
}
```

---

## 3. Factory Updates

### New Factory: BookingSlotCircuitFactory
**File:** `database/factories/BookingSlotCircuitFactory.php` (create new)

```php
<?php

namespace Database\Factories;

use App\Models\BookingSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingSlotCircuitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_slot_id' => BookingSlot::factory(),
            'name' => null,
        ];
    }

    public function named(string $name): BookingSlotCircuitFactory
    {
        return $this->state(fn (array $attributes) => ['name' => $name]);
    }
}
```

### Rename & Update: BookingSlotCircuitWorkoutFactory
**File:** Rename `database/factories/BookingSlotWorkoutFactory.php` → `database/factories/BookingSlotCircuitWorkoutFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\BookingSlotCircuit;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingSlotCircuitWorkoutFactory extends Factory
{
    protected $model = \App\Models\BookingSlotCircuitWorkout::class;

    public function definition(): array
    {
        return [
            'booking_slot_circuit_id' => BookingSlotCircuit::factory(),
            'workout_id' => Workout::factory(),
        ];
    }
}
```

### Rename: BookingSlotCircuitWorkoutSetFactory
**File:** Rename `database/factories/BookingSlotWorkoutSetFactory.php` → `database/factories/BookingSlotCircuitWorkoutSetFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingSlotCircuitWorkoutSetFactory extends Factory
{
    protected $model = \App\Models\BookingSlotCircuitWorkoutSet::class;

    public function definition(): array
    {
        return [
            'booking_slot_circuit_workout_id' => BookingSlotCircuitWorkout::factory(),
            'reps' => fake()->numberBetween(8, 15),
            'weight_in_kg' => fake()->randomFloat(1, 5, 50),
            'duration_in_seconds' => null,
        ];
    }
}
```

---

## 4. Seeder Updates

### Rename & Update: BookingSlotCircuitWorkoutSeeder
**File:** Rename `database/seeders/BookingSlotWorkoutSeeder.php` → `database/seeders/BookingSlotCircuitWorkoutSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkoutSet;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class BookingSlotCircuitWorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $pastBookingSlots = BookingSlot::query()->where('end_time', '<', now())->get();

        foreach ($pastBookingSlots as $bookingSlot) {
            $this->seedWorkoutsForBookingSlot($bookingSlot);
        }
    }

    private function seedWorkoutsForBookingSlot(BookingSlot $bookingSlot): void
    {
        // Randomly create 1-2 circuits per session
        $circuitCount = fake()->numberBetween(1, 2);

        for ($i = 0; $i < $circuitCount; $i++) {
            $circuit = BookingSlotCircuit::query()->create([
                'booking_slot_id' => $bookingSlot->id,
                'name' => $this->generateCircuitName(),
            ]);

            $this->seedWorkoutsForCircuit($circuit);
        }
    }

    private function generateCircuitName(): ?string
    {
        return fake()->randomElement([
            'Full Body',
            'Upper Body Circuit',
            'Lower Body Circuit',
            'Core Circuit',
            'Cardio Circuit',
            'Strength Circuit',
        ]);
    }

    private function seedWorkoutsForCircuit(BookingSlotCircuit $circuit): void
    {
        $categories = $this->selectRandomCategories();
        $selectedWorkouts = $this->getWorkoutsByCategories($categories);

        foreach ($selectedWorkouts as $workout) {
            $bookingSlotCircuitWorkout = BookingSlotCircuitWorkout::query()->create([
                'booking_slot_circuit_id' => $circuit->id,
                'workout_id' => $workout->id,
            ]);

            $this->createSetsForWorkout($bookingSlotCircuitWorkout);
        }
    }

    private function selectRandomCategories(): array
    {
        $allCategories = Category::cases();
        $groupCount = fake()->numberBetween(2, 3);

        return fake()->randomElements($allCategories, $groupCount);
    }

    private function getWorkoutsByCategories(array $categories): array
    {
        $selectedWorkouts = [];

        foreach ($categories as $category) {
            $workouts = Workout::query()
                ->where('category', $category)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            $selectedWorkouts = array_merge($selectedWorkouts, $workouts->all());
        }

        return $selectedWorkouts;
    }

    private function createSetsForWorkout(BookingSlotCircuitWorkout $bookingSlotCircuitWorkout): void
    {
        $faker = fake();
        $setsCount = 3;
        $isWeighted = $faker->boolean(70);
        $isTimed = !$isWeighted;

        for ($i = 0; $i < $setsCount; $i++) {
            BookingSlotCircuitWorkoutSet::query()->create([
                'booking_slot_circuit_workout_id' => $bookingSlotCircuitWorkout->id,
                'reps' => $isWeighted ? 12 : 1,
                'weight_in_kg' => $isWeighted ? $this->generateWeight() : null,
                'duration_in_seconds' => $isTimed ? $faker->numberBetween(30, 180) : null,
            ]);
        }
    }

    private function generateWeight(): float
    {
        $faker = fake();
        $increments = [1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5];
        $multiplier = $faker->numberBetween(1, 7);
        $baseWeight = $faker->randomElement($increments);

        return min(35, $baseWeight * $multiplier);
    }
}
```

### Update DatabaseSeeder
**File:** `database/seeders/DatabaseSeeder.php`

Update the seeder call:
```php
// Change from:
$this->call(BookingSlotWorkoutSeeder::class);

// To:
$this->call(BookingSlotCircuitWorkoutSeeder::class);
```

---

## 5. Resource Updates

### New Resource: BookingSlotCircuitResource
**File:** `app/Http/Resources/BookingSlotCircuitResource.php` (create new)

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSlotCircuitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'workouts' => $this->whenLoaded('circuitWorkouts',
                fn () => BookingSlotCircuitWorkoutResource::collection($this->circuitWorkouts)
            ),
        ];
    }
}
```

### Rename & Update: BookingSlotCircuitWorkoutResource
**File:** Rename `app/Http/Resources/BookingSlotWorkoutResource.php` → `app/Http/Resources/BookingSlotCircuitWorkoutResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSlotCircuitWorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'circuit_id' => $this->booking_slot_circuit_id,
            'name' => $this->whenLoaded('workout', fn () => $this->workout->name),
            'category' => $this->whenLoaded('workout', fn () => $this->workout->category->value),
            'sets' => $this->whenLoaded('sets', function () {
                return $this->sets->map(fn ($set) => [
                    'reps' => $set->reps,
                    'weight_in_kg' => $set->weight_in_kg,
                    'duration_in_seconds' => $set->duration_in_seconds,
                ]);
            }),
            'delete_url' => route('admin.bookings-slots.workout.destroy', [
                'bookingSlot' => $this->circuit->booking_slot_id,
                'bookingSlotCircuitWorkout' => $this->id,
            ]),
        ];
    }
}
```

### Update BookingSlotResource
**File:** `app/Http/Resources/BookingSlotResource.php`

Update to use circuits instead of direct workouts:
```php
return [
    // ... existing fields
    'circuits' => $this->whenLoaded('circuits',
        fn () => BookingSlotCircuitResource::collection($this->circuits)
    ),
    // ... rest of fields
];
```

---

## 6. Controller Updates

### Rename & Update: BookingSlotCircuitWorkoutController
**File:** Rename `app/Http/Controllers/Admin/BookingSlotWorkoutController.php` → `app/Http/Controllers/Admin/BookingSlotCircuitWorkoutController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingSlotCircuitWorkoutRequest;
use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingSlotCircuitWorkoutController extends Controller
{
    public function create(BookingSlot $bookingSlot): Response
    {
        $bookingSlot->load([
            'booking',
            'booking.member',
            'booking.trainer',
        ]);

        $workouts = Workout::query()->orderBy('category')->orderBy('name')->get();

        return Inertia::render('Admin/BookingSlotWorkout/Create', [
            'bookingSlot' => BookingSlotResource::make($bookingSlot),
            'workouts' => WorkoutResource::collection($workouts),
        ]);
    }

    public function store(BookingSlotCircuitWorkoutRequest $request, BookingSlot $bookingSlot): RedirectResponse
    {
        // Get or create default circuit for this booking slot
        $circuit = $bookingSlot->circuits()->firstOrCreate(
            ['name' => 'Full Body']
        );

        foreach ($request->input('workouts', []) as $workoutData) {
            $bookingSlotCircuitWorkout = BookingSlotCircuitWorkout::query()->create([
                'booking_slot_circuit_id' => $circuit->id,
                'workout_id' => $workoutData['id'],
            ]);

            $sets = [];
            $weights = $workoutData['weight_in_kg'] ?? [];
            $durations = $workoutData['duration_in_seconds'] ?? [];
            $reps = $workoutData['reps'] ?? [];

            for ($i = 0; $i < max(count($weights), count($durations), count($reps)); $i++) {
                $sets[] = [
                    'reps' => $reps[$i],
                    'weight_in_kg' => $weights[$i] ?? null,
                    'duration_in_seconds' => $durations[$i] ?? null,
                ];
            }

            $bookingSlotCircuitWorkout->sets()->createMany($sets);
        }

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }

    public function destroy(BookingSlot $bookingSlot, BookingSlotCircuitWorkout $bookingSlotCircuitWorkout): RedirectResponse
    {
        $bookingSlotCircuitWorkout->delete();

        return redirect()->route('admin.bookings-slots.show', $bookingSlot->id);
    }
}
```

### Update BookingSlotsController
**File:** `app/Http/Controllers/Admin/BookingSlotsController.php`

Update eager loading in `show()` method:
```php
public function show(BookingSlot $bookingSlot): Response
{
    $bookingSlot->load([
        'booking',
        'booking.member',
        'booking.trainer',
        'circuits.circuitWorkouts.workout',
        'circuits.circuitWorkouts.sets',
    ]);

    return Inertia::render('Admin/BookingsSlots/Show', [
        'bookingSlot' => BookingSlotResource::make($bookingSlot),
        'bookingId' => request('booking_id'),
    ]);
}
```

### Rename Form Request
**File:** Rename `app/Http/Requests/Admin/BookingSlotWorkoutRequest.php` → `app/Http/Requests/Admin/BookingSlotCircuitWorkoutRequest.php`

Update class name:
```php
class BookingSlotCircuitWorkoutRequest extends FormRequest
{
    // Validation rules remain the same
}
```

---

## 7. Testing Strategy

### New Test File: BookingSlotCircuitTest.php
**File:** `tests/Feature/Models/BookingSlotCircuitTest.php` (create new)

Test model relationships:
- BookingSlot has many circuits
- Circuit has many circuitWorkouts
- Deleting circuit cascades to workouts
- Deleting booking slot cascades to circuits

### Rename & Update: BookingSlotCircuitWorkoutTest.php
**File:** Rename `tests/Feature/Admin/BookingSlotWorkoutTest.php` → `tests/Feature/Admin/BookingSlotCircuitWorkoutTest.php`

Update tests to use new models:
- Test creating workouts creates/uses default "Full Body" circuit
- Test workouts belong to circuit (not directly to booking slot)
- Test deleting workouts
- Update all model references and relationship names

---

## 8. Routes Update

**File:** `routes/web.php`

Update route parameter bindings:
```php
// Change from:
Route::delete('/bookings-slots/{bookingSlot}/workout/{bookingSlotWorkout}',
    [BookingSlotWorkoutController::class, 'destroy'])
    ->name('admin.bookings-slots.workout.destroy');

// To:
Route::delete('/bookings-slots/{bookingSlot}/workout/{bookingSlotCircuitWorkout}',
    [BookingSlotCircuitWorkoutController::class, 'destroy'])
    ->name('admin.bookings-slots.workout.destroy');
```

---

## 9. Implementation Checklist

### Phase 1: Database Migration
- [ ] Truncate `booking_slot_workouts` and `booking_slot_workout_sets` in production
- [ ] Create `2025_12_27_000001_create_circuits_and_update_workouts.php`
- [ ] Test migration locally with `php artisan migrate`
- [ ] Test rollback locally with `php artisan migrate:rollback`
- [ ] Run migration in production

### Phase 2: Models (Rename & Update)
- [ ] Create `BookingSlotCircuit` model
- [ ] Rename `BookingSlotWorkout` → `BookingSlotCircuitWorkout`
- [ ] Rename `BookingSlotWorkoutSet` → `BookingSlotCircuitWorkoutSet`
- [ ] Update `BookingSlot` model (add circuits relationship)
- [ ] Update all relationship names (circuit, circuitWorkouts, circuitWorkout)

### Phase 3: Factories & Seeders (Rename & Update)
- [ ] Create `BookingSlotCircuitFactory`
- [ ] Rename `BookingSlotWorkoutFactory` → `BookingSlotCircuitWorkoutFactory`
- [ ] Rename `BookingSlotWorkoutSetFactory` → `BookingSlotCircuitWorkoutSetFactory`
- [ ] Rename `BookingSlotWorkoutSeeder` → `BookingSlotCircuitWorkoutSeeder`
- [ ] Update `DatabaseSeeder` to call renamed seeder
- [ ] Test seeder: `php artisan db:seed --class=BookingSlotCircuitWorkoutSeeder`

### Phase 4: Resources (Rename & Update)
- [ ] Create `BookingSlotCircuitResource`
- [ ] Rename `BookingSlotWorkoutResource` → `BookingSlotCircuitWorkoutResource`
- [ ] Update `BookingSlotResource` (add circuits)
- [ ] Update relationship references (circuitWorkouts, circuit)

### Phase 5: Controllers & Requests (Rename & Update)
- [ ] Rename `BookingSlotWorkoutController` → `BookingSlotCircuitWorkoutController`
- [ ] Rename `BookingSlotWorkoutRequest` → `BookingSlotCircuitWorkoutRequest`
- [ ] Update `BookingSlotsController::show()` eager loading
- [ ] Update `routes/web.php` route bindings

### Phase 6: Testing (Rename & Update)
- [ ] Create `BookingSlotCircuitTest.php`
- [ ] Rename `BookingSlotWorkoutTest.php` → `BookingSlotCircuitWorkoutTest.php`
- [ ] Update all test imports and assertions
- [ ] Run full test suite: `php artisan test`
- [ ] Run Pint formatter: `./vendor/bin/pint`

### Phase 7: Frontend (Future Work - Not in this plan)
- [ ] Update session show page to display circuits instead of categories
- [ ] Update workout creation UI to support circuits
- [ ] Add circuit management features (Trello-like UI)

---

## 10. Critical Files Reference

### New Files to Create
1. `database/migrations/2025_12_27_000001_create_circuits_and_update_workouts.php`
2. `app/Models/BookingSlotCircuit.php`
3. `app/Models/BookingSlotCircuitWorkout.php` (renamed from BookingSlotWorkout)
4. `app/Models/BookingSlotCircuitWorkoutSet.php` (renamed from BookingSlotWorkoutSet)
5. `database/factories/BookingSlotCircuitFactory.php`
6. `database/factories/BookingSlotCircuitWorkoutFactory.php` (renamed)
7. `database/factories/BookingSlotCircuitWorkoutSetFactory.php` (renamed)
8. `database/seeders/BookingSlotCircuitWorkoutSeeder.php` (renamed)
9. `app/Http/Resources/BookingSlotCircuitResource.php`
10. `app/Http/Resources/BookingSlotCircuitWorkoutResource.php` (renamed)
11. `app/Http/Controllers/Admin/BookingSlotCircuitWorkoutController.php` (renamed)
12. `app/Http/Requests/Admin/BookingSlotCircuitWorkoutRequest.php` (renamed)
13. `tests/Feature/Models/BookingSlotCircuitTest.php`
14. `tests/Feature/Admin/BookingSlotCircuitWorkoutTest.php` (renamed)

### Files to Update
1. `app/Models/BookingSlot.php` - Add circuits relationship
2. `app/Http/Resources/BookingSlotResource.php` - Add circuits
3. `app/Http/Controllers/Admin/BookingSlotsController.php` - Update eager loading
4. `database/seeders/DatabaseSeeder.php` - Update seeder call
5. `routes/web.php` - Update route bindings
6. `tests/Pest.php` - May need helper function updates

### Files to Delete (after renaming)
1. `app/Models/BookingSlotWorkout.php`
2. `app/Models/BookingSlotWorkoutSet.php`
3. `database/factories/BookingSlotWorkoutFactory.php`
4. `database/factories/BookingSlotWorkoutSetFactory.php`
5. `database/seeders/BookingSlotWorkoutSeeder.php`
6. `app/Http/Resources/BookingSlotWorkoutResource.php`
7. `app/Http/Controllers/Admin/BookingSlotWorkoutController.php`
8. `app/Http/Requests/Admin/BookingSlotWorkoutRequest.php`
9. `tests/Feature/Admin/BookingSlotWorkoutTest.php`

---

## 11. Deployment Steps (Production)

```bash
# 1. SSH into production server

# 2. Truncate tables (data will be lost!)
mysql -u your_user -p your_database
> TRUNCATE TABLE booking_slot_workout_sets;
> TRUNCATE TABLE booking_slot_workouts;
> exit;

# 3. Pull latest code
git pull origin workout-circuits

# 4. Install composer dependencies (for doctrine/dbal if needed)
composer install

# 5. Run migration
php artisan migrate

# 6. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 7. Reseed database (optional - creates fresh test data)
php artisan db:seed --class=BookingSlotCircuitWorkoutSeeder
```

---

## 12. Summary

### Perfect Naming Consistency

| Item | Name |
|------|------|
| **Circuits Table** | `booking_slot_circuits` |
| **Workouts Table** | `booking_slot_circuit_workouts` |
| **Sets Table** | `booking_slot_circuit_workout_sets` |
| **Sets FK Column** | `booking_slot_circuit_workout_id` |
| **Model: Circuit** | `BookingSlotCircuit` |
| **Model: Workout** | `BookingSlotCircuitWorkout` |
| **Model: Set** | `BookingSlotCircuitWorkoutSet` |
| **$table Property** | ✅ Not needed anywhere! |

### Clean Relationship Names

| Model | Relationship | Returns |
|-------|--------------|---------|
| BookingSlot | `circuits()` | BookingSlotCircuit[] |
| BookingSlotCircuit | `bookingSlot()` | BookingSlot |
| BookingSlotCircuit | `circuitWorkouts()` | BookingSlotCircuitWorkout[] |
| BookingSlotCircuitWorkout | `circuit()` | BookingSlotCircuit |
| BookingSlotCircuitWorkout | `workout()` | Workout |
| BookingSlotCircuitWorkout | `sets()` | BookingSlotCircuitWorkoutSet[] |
| BookingSlotCircuitWorkoutSet | `circuitWorkout()` | BookingSlotCircuitWorkout |

### Semantic Eager Loading

```php
// Beautiful and readable!
'circuits.circuitWorkouts.workout'
'circuits.circuitWorkouts.sets'
```

---

## 13. Notes

- **Simplified**: No data migration command needed - we truncate instead
- **Single migration**: All schema changes in one file
- **Perfect naming**: Tables and models match exactly - no `$table` properties
- **Clean relationships**: Semantic names without prefixes (`circuit`, `circuitWorkouts`, `sets`)
- **NO position fields**: Order doesn't matter for circuits or workouts
- **Clean hierarchy**: BookingSlot → Circuit → CircuitWorkout → Sets
- Frontend Vue component updates are **not included** - separate task
- Category field on Workout model is **not removed** (future-proofing)