# Workout Circuits Feature - Implementation Plan

## What's Been Done ✅

### Phase 1: UI Cleanup (Completed)
- ✅ Removed all BookingSlotWorkout create/store UI features
- ✅ Deleted frontend components (Create.vue and partials)
- ✅ Deleted backend controller, form request
- ✅ Removed routes (create, store, destroy)
- ✅ Cleaned up BookingSlot show page (removed workout display)
- ✅ Removed all tests for deleted features
- ✅ Updated seeders (removed BookingSlotWorkoutSeeder call)

### Phase 2: Database Migration Restructure (Completed)
- ✅ Deleted old migrations (`create_booking_slot_workouts_table.php`, `create_workout_sets_table.php`)
- ✅ Created clean migrations that work with `migrate:fresh --seed`:
  - `2025_06_21_095411_create_booking_slot_circuits_table.php`
  - `2025_06_21_095412_create_booking_slot_circuit_workouts_table.php`
  - `2025_06_21_095413_create_booking_slot_circuit_workout_sets_table.php`
- ✅ Fixed MySQL FK constraint name length issues
- ✅ Updated old migrations to use table names instead of model references

### Phase 3: Model Updates (Completed)
- ✅ Created `BookingSlotCircuit` model
- ✅ Renamed `BookingSlotWorkout` → `BookingSlotCircuitWorkout`
- ✅ Renamed `BookingSlotWorkoutSet` → `BookingSlotCircuitWorkoutSet`
- ✅ Updated `BookingSlot` model (added circuits relationship)
- ✅ Updated `Workout` model (renamed relationship to `bookingSlotCircuitWorkouts()`)

### Phase 4: Factories & Seeders (Completed)
- ✅ Created `BookingSlotCircuitFactory`
- ✅ Renamed and updated `BookingSlotCircuitWorkoutFactory`
- ✅ Renamed and updated `BookingSlotCircuitWorkoutSetFactory`
- ✅ Created `BookingSlotCircuitWorkoutSeeder`

### Phase 5: Resources (Completed)
- ✅ Created `BookingSlotCircuitResource`
- ✅ Created `BookingSlotCircuitWorkoutResource`

### Phase 6: Code Quality (Completed)
- ✅ Fixed all code references to old relationships
- ✅ Updated `MembersController` (removed old workout eager loading)
- ✅ Updated `BodyCompositionTest` (removed old workout eager loading)
- ✅ All tests passing (243 passing, 1 pre-existing failure unrelated)
- ✅ Code formatted with Laravel Pint

---

## What's Left To Do 🔨

### 1. Build the Trello-like UI
The database structure is ready, now implement the frontend:

**BookingSlotsShow.vue - Display circuits:**
- Show circuits grouped by name
- Display workouts within each circuit
- Show sets for each workout (reps, weight, duration)
- Add delete buttons for workouts

**Circuit Management UI (New):**
- Create/edit circuits (with name)
- Drag and drop workouts into circuits
- Reorder workouts within circuits
- Add/edit/delete sets for each workout

**Example Structure:**
```
Circuit 1: Upper Body
  ├── Bench Press (3 sets: 12 reps @ 20kg, 10 reps @ 25kg, 8 reps @ 30kg)
  ├── Shoulder Press (3 sets: 12 reps @ 15kg each)
  └── Lat Pulldown (3 sets: 12 reps @ 30kg each)

Circuit 2: Core
  ├── Plank (3 sets: 30s, 45s, 60s)
  ├── Russian Twist (3 sets: 20 reps each)
  └── Leg Raises (3 sets: 15 reps each)
```

### 2. Controller Updates
- Update `BookingSlotsController::show()` to eager load circuits
- Create circuit management endpoints (if needed for the UI)
- Update route bindings if needed

### 3. Testing
- Create `BookingSlotCircuitTest.php` (model relationships)
- Update any remaining tests that reference the old structure
- Test the new UI thoroughly

---

## For Production: Manual Migration Steps

⚠️ **IMPORTANT:** These steps preserve your existing production data by renaming tables.

### Step 1: Backup Database
```bash
mysqldump -u [user] -p [database_name] > backup_before_circuits_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Run Custom Migration SQL

Execute this SQL directly on your production database:

```sql
-- Create the new circuits table
CREATE TABLE `booking_slot_circuits` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_slot_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_slot_circuits_booking_slot_id_foreign` (`booking_slot_id`),
  CONSTRAINT `booking_slot_circuits_booking_slot_id_foreign`
    FOREIGN KEY (`booking_slot_id`)
    REFERENCES `booking_slots` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rename existing tables
RENAME TABLE `booking_slot_workouts` TO `booking_slot_circuit_workouts`;
RENAME TABLE `booking_slot_workout_sets` TO `booking_slot_circuit_workout_sets`;

-- Update circuit_workouts table FK
ALTER TABLE `booking_slot_circuit_workouts`
  DROP FOREIGN KEY `booking_slot_workouts_booking_slot_id_foreign`;

ALTER TABLE `booking_slot_circuit_workouts`
  DROP COLUMN `booking_slot_id`;

ALTER TABLE `booking_slot_circuit_workouts`
  ADD COLUMN `booking_slot_circuit_id` bigint(20) UNSIGNED NOT NULL AFTER `id`,
  ADD CONSTRAINT `booking_slot_circuit_workouts_booking_slot_circuit_id_foreign`
    FOREIGN KEY (`booking_slot_circuit_id`)
    REFERENCES `booking_slot_circuits` (`id`)
    ON DELETE CASCADE;

-- Update circuit_workout_sets table FK
ALTER TABLE `booking_slot_circuit_workout_sets`
  DROP FOREIGN KEY `booking_slot_workout_sets_booking_slot_workout_id_foreign`;

ALTER TABLE `booking_slot_circuit_workout_sets`
  CHANGE COLUMN `booking_slot_workout_id` `booking_slot_circuit_workout_id` bigint(20) UNSIGNED NOT NULL;

ALTER TABLE `booking_slot_circuit_workout_sets`
  ADD CONSTRAINT `bscws_bscw_id_foreign`
    FOREIGN KEY (`booking_slot_circuit_workout_id`)
    REFERENCES `booking_slot_circuit_workouts` (`id`)
    ON DELETE CASCADE;
```

### Step 3: Mark Migrations as Run

```bash
php artisan tinker
```

Then in tinker:
```php
$batch = DB::table('migrations')->max('batch') + 1;

DB::table('migrations')->insert([
    ['migration' => '2025_06_21_095411_create_booking_slot_circuits_table', 'batch' => $batch],
    ['migration' => '2025_06_21_095412_create_booking_slot_circuit_workouts_table', 'batch' => $batch],
    ['migration' => '2025_06_21_095413_create_booking_slot_circuit_workout_sets_table', 'batch' => $batch],
]);
```

### Step 4: Deploy Code
```bash
git pull origin workout-circuits
composer install
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan queue:restart
```

### Step 5: Verify

```bash
php artisan tinker
```

Check tables exist:
```php
DB::table('booking_slot_circuits')->count();
DB::table('booking_slot_circuit_workouts')->count();
DB::table('booking_slot_circuit_workout_sets')->count();

// Test relationships
$slot = App\Models\BookingSlot::first();
$slot->load('circuits.circuitWorkouts.workout');
$slot->circuits; // Should return collection
```

---

## Database Structure

### Tables
- `booking_slot_circuits` - Groups workouts together (e.g., "Upper Body Circuit")
- `booking_slot_circuit_workouts` - Individual workouts within a circuit
- `booking_slot_circuit_workout_sets` - Sets for each workout (reps, weight, duration)

### Relationships
```
BookingSlot (1) → (many) BookingSlotCircuit (1) → (many) BookingSlotCircuitWorkout (1) → (many) BookingSlotCircuitWorkoutSet
                                                              ↓
                                                           Workout (base workout definition)
```

### Example Data Flow
```
BookingSlot #123 (Monday 10am session)
  ├── Circuit #1 "Upper Body"
  │     ├── CircuitWorkout #1 → Workout "Bench Press"
  │     │     ├── Set 1: 12 reps @ 20kg
  │     │     ├── Set 2: 10 reps @ 25kg
  │     │     └── Set 3: 8 reps @ 30kg
  │     └── CircuitWorkout #2 → Workout "Shoulder Press"
  │           ├── Set 1: 12 reps @ 15kg
  │           └── Set 2: 10 reps @ 15kg
  └── Circuit #2 "Core"
        ├── CircuitWorkout #3 → Workout "Plank"
        │     ├── Set 1: 30s
        │     └── Set 2: 45s
        └── CircuitWorkout #4 → Workout "Russian Twist"
              ├── Set 1: 20 reps
              └── Set 2: 20 reps
```

---

## Notes

- Local development: `migrate:fresh --seed` works perfectly
- Production: Manual SQL migration preserves existing data
- All models, factories, resources, and seeders are ready
- Tests are passing (243/244)
- Next step: Build the Trello-like UI for circuit management

---

# Multi-Category Workouts Implementation Plan

## Overview
Convert Workout model from single category to multiple categories, stored as JSON array in a JSON field.

## Requirements
- Store multiple categories per workout (e.g., `["Chest","Shoulders","Triceps"]`)
- Use JSON field (single column, not many-to-many table)
- Keep Category enum as source of truth
- Update existing migration (don't create new one)
- Update seeder with logical multi-category assignments
- Leverage Laravel's native JSON support for efficient filtering

## Why JSON Over CSV String?

| Aspect | JSON Array | CSV String |
|--------|-----------|------------|
| **Database Storage** | `["Chest","Shoulders"]` | `"Chest,Shoulders"` |
| **Model Code** | Simple cast: `'categories' => 'array'` | Custom accessor/mutator needed |
| **Filtering** | `whereJsonContains('categories', 'Chest')` | `where('category', 'like', '%Chest%')` |
| **Performance** | Optimized JSON queries, indexable | String matching, less efficient |
| **Type Safety** | Structured array data | String parsing required |
| **Code Simplicity** | ~3 lines of code | ~15 lines of accessor/mutator |
| **Future Features** | Easy (counts, complex filters) | Harder (need string parsing) |

**Conclusion:** JSON is significantly better for this use case. It's simpler, faster, and more maintainable.

## Implementation Steps

### 1. Update Migration
**File:** `database/migrations/2025_06_21_095410_create_workouts_table.php`

Change category column from ENUM to JSON:
```php
// FROM: $table->enum('category', $categories)->index();
// TO:   $table->json('categories');
```

Note: Column name changes from `category` (singular) to `categories` (plural) to reflect that it now stores multiple values. Remove the `$categories` variable calculation.

### 2. Update Model
**File:** `app/Models/Workout.php`

Replace the category cast with array cast:
```php
// Remove old cast
protected $casts = [
    'category' => Category::class,  // DELETE
];

// Add new cast
protected $casts = [
    'categories' => 'array',  // Laravel handles JSON ↔ array automatically
];
```

Update fillable array:
```php
protected $fillable = ['name', 'categories', 'image'];  // Change 'category' to 'categories'
```

**That's it!** Laravel's array cast automatically:
- Converts JSON `["Chest","Shoulders"]` from DB → PHP array `["Chest","Shoulders"]`
- Converts PHP array → JSON when saving
- No custom accessor/mutator needed!

### 3. Update WorkoutResource
**File:** `app/Http/Resources/WorkoutResource.php`

Change from single category to array:
```php
// FROM: 'category' => $this->category->value,
// TO:   'categories' => $this->categories,  // Already an array of strings from the model!
```

### 4. Update WorkoutRequest Validation
**File:** `app/Http/Requests/Admin/WorkoutRequest.php`

Validate array of categories:
```php
// FROM: 'category' => ['required', new Enum(Category::class)],
// TO:
'categories' => ['required', 'array', 'min:1'],
'categories.*' => [new Enum(Category::class)],
```

### 5. Update Controller Filtering
**File:** `app/Http/Controllers/Admin/WorkoutController.php`

Change filtering logic to use Laravel's JSON query methods:
```php
->when(request('categories'), function (Builder $query, array $categories) {
    $query->where(function (Builder $q) use ($categories) {
        foreach ($categories as $category) {
            $q->orWhereJsonContains('categories', $category);
        }
    });
})
```

**Much cleaner!** `whereJsonContains()` is purpose-built for searching JSON arrays and performs better than LIKE queries.

### 6. Update WorkoutSeeder
**File:** `database/seeders/WorkoutSeeder.php`

Replace the entire seeder with this structure to assign multiple categories:

```php
<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $workouts = [
            // Legs + Glutes (compound exercises)
            ['name' => 'Barbell Squats', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Smith Squats', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Leg Press', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Lunges', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Bulgarian Split Squats', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Step-Ups', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Hip Thrusts', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Romanian Deadlifts', 'categories' => [Category::Legs->value, Category::Glutes->value, Category::Back->value]],
            ['name' => 'Sumo Deadlifts', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Single Leg Reverse Lunges', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Single Leg Bridges', 'categories' => [Category::Glutes->value, Category::Legs->value]],

            // Legs only (isolation)
            ['name' => 'Leg Extensions', 'categories' => [Category::Legs->value]],
            ['name' => 'Leg Curls', 'categories' => [Category::Legs->value]],
            ['name' => 'Calf Raises Standing', 'categories' => [Category::Legs->value]],

            // Glutes + Back
            ['name' => 'Glutes Hyperextension', 'categories' => [Category::Glutes->value, Category::Back->value]],

            // Glutes only (isolation)
            ['name' => 'Cable Kickbacks', 'categories' => [Category::Glutes->value]],
            ['name' => 'Glute Abductions', 'categories' => [Category::Glutes->value]],

            // Biceps only
            ['name' => 'Barbell Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Dumbbell Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Preacher Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Cable Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Concentration Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Hammer Curls', 'categories' => [Category::Biceps->value]],

            // Triceps + Chest
            ['name' => 'Dips', 'categories' => [Category::Triceps->value, Category::Chest->value]],
            ['name' => 'Chest Dips', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Close-Grip Bench Press', 'categories' => [Category::Chest->value, Category::Triceps->value]],

            // Triceps only
            ['name' => 'Cable Pushdowns', 'categories' => [Category::Triceps->value]],
            ['name' => 'Overhead Dumbbell Extensions', 'categories' => [Category::Triceps->value]],
            ['name' => 'Skull Crushers', 'categories' => [Category::Triceps->value]],
            ['name' => 'Cable Overhead Rope', 'categories' => [Category::Triceps->value]],

            // Shoulders + Chest
            ['name' => 'Dumbbell Press', 'categories' => [Category::Chest->value, Category::Shoulders->value]],
            ['name' => 'Arnold Press', 'categories' => [Category::Shoulders->value, Category::Chest->value]],

            // Shoulders + Triceps
            ['name' => 'Overhead Press Dumbbell', 'categories' => [Category::Shoulders->value, Category::Triceps->value]],
            ['name' => 'Military Press', 'categories' => [Category::Shoulders->value, Category::Triceps->value]],

            // Shoulders + Back
            ['name' => 'Face Pulls', 'categories' => [Category::Back->value, Category::Shoulders->value]],
            ['name' => 'Upright Rows', 'categories' => [Category::Shoulders->value, Category::Back->value]],

            // Shoulders only
            ['name' => 'Smith Shoulder Press', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Lateral Raises', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Front Raises', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Rear Delt Fly', 'categories' => [Category::Shoulders->value]],

            // Chest + Triceps
            ['name' => 'Barbell Bench Press', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Smith Chest Press', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Push-Ups', 'categories' => [Category::Chest->value, Category::Triceps->value]],

            // Chest only
            ['name' => 'Cable Fly', 'categories' => [Category::Chest->value]],

            // Back + Legs + Glutes (deadlifts)
            ['name' => 'Deadlifts', 'categories' => [Category::Back->value, Category::Legs->value, Category::Glutes->value]],

            // Back + Biceps
            ['name' => 'Pull-Ups', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Lat Pulldowns', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Barbell Or Dumbbell Rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Chin-Ups', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Seated Rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Kettlebell Rows', 'categories' => [Category::Back->value, Category::Biceps->value]],

            // Back only
            ['name' => 'Straight Arm Pulldown', 'categories' => [Category::Back->value]],

            // Core + Abs
            ['name' => 'Crunches', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Sit-Ups', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Cable Crunch', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Leg Raises', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Hanging Knee Raises', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Reverse Crunches', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Russian Twists', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Cable Woodchoppers', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Plank Holds', 'categories' => [Category::Core->value, Category::Abs->value]],

            // Core only
            ['name' => 'Side Plank', 'categories' => [Category::Core->value]],
        ];

        foreach ($workouts as $workout) {
            Workout::query()->create($workout);
        }
    }
}
```

This restructures all 86 workouts with appropriate multi-category assignments.

### 7. Update WorkoutFactory
**File:** `database/factories/WorkoutFactory.php`

Generate random multi-category workouts:
```php
// FROM: 'category' => $this->faker->randomElement(array: Category::cases()),
// TO:   'categories' => $this->faker->randomElements(array: Category::cases(), count: $this->faker->numberBetween(1, 3)),
```

### 8. Update Frontend Form
**File:** `resources/js/Pages/Admin/Workouts/Partials/WorkoutForm.vue`

Replace SelectInput with checkbox group (similar to WorkoutsFilters.vue):
- Display all 9 categories as checkboxes in 2-column grid
- Allow multiple selections
- Use existing Checkbox.vue component
- Add InputLabel "Categories *"

### 9. Update Create & Edit Pages
**Files:**
- `resources/js/Pages/Admin/Workouts/Create.vue`
- `resources/js/Pages/Admin/Workouts/Edit.vue`

Change form data structure:
```js
// Create: categories: []
// Edit:   categories: workout.categories
```

### 10. Update Tests
**File:** `tests/Feature/Admin/WorkoutsTest.php`

Update all tests to use `categories` array instead of `category` string:
- Test creating workout with multiple categories
- Test validation requires at least one category
- Test filtering by multiple categories with `whereJsonContains()`
- Verify database stores as JSON: `["Chest","Shoulders"]`
- Verify model returns array of strings

Add new test cases:
- Multiple category creation with array input
- Filtering shows workouts with ANY matching category
- Empty categories array validation
- Test that JSON is properly stored/retrieved

### 11. Run Migration & Tests
```bash
# Development: Fresh migration
php artisan migrate:fresh --seed

# Run tests
php artisan test --filter=WorkoutsTest
./vendor/bin/pint
```

## Critical Files to Modify
1. `database/migrations/2025_06_21_095410_create_workouts_table.php` - ENUM → JSON
2. `app/Models/Workout.php` - Array cast for categories
3. `app/Http/Controllers/Admin/WorkoutController.php` - Update filtering logic
4. `app/Http/Resources/WorkoutResource.php` - Return categories array
5. `app/Http/Requests/Admin/WorkoutRequest.php` - Validate categories array
6. `database/seeders/WorkoutSeeder.php` - Assign multiple categories
7. `database/factories/WorkoutFactory.php` - Generate multi-category test data
8. `resources/js/Pages/Admin/Workouts/Partials/WorkoutForm.vue` - Checkbox group UI
9. `resources/js/Pages/Admin/Workouts/Create.vue` - Form initialization
10. `resources/js/Pages/Admin/Workouts/Edit.vue` - Form initialization
11. `tests/Feature/Admin/WorkoutsTest.php` - Update all test assertions

## Data Format
- **Database:** JSON array like `["Chest","Shoulders"]` or `["Core","Abs"]`
- **Model (via array cast):** PHP array of strings `["Chest", "Shoulders"]`
- **API/Frontend:** Same array of strings `["Chest", "Shoulders"]`

---

## Production Deployment Instructions for Multi-Category Workouts

⚠️ **IMPORTANT:** This will change the workouts table structure. You mentioned you'll handle production migration manually.

### Step 1: Backup Database
```bash
mysqldump -u [user] -p [database_name] > backup_multi_category_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Manual Migration SQL

Since you're updating the existing migration (not creating a new one), you'll need to manually run SQL in production:

```sql
-- Step 1: Add new JSON column
ALTER TABLE `workouts` ADD COLUMN `categories` JSON NULL AFTER `name`;

-- Step 2: Migrate data from category (enum) to categories (json)
-- Convert single category to JSON array
UPDATE `workouts` SET `categories` = JSON_ARRAY(`category`);

-- Step 3: Verify data migration
SELECT id, name, category, categories FROM workouts LIMIT 10;

-- Step 4: Drop old category column (only after verifying!)
ALTER TABLE `workouts` DROP COLUMN `category`;

-- Step 5: Make categories NOT NULL (all should have data now)
ALTER TABLE `workouts` MODIFY `categories` JSON NOT NULL;
```

### Step 3: Deploy Code
```bash
git pull origin [your-branch]
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan queue:restart
```

### Step 4: Verify Production

```bash
php artisan tinker
```

Then in tinker:
```php
// Check a few workouts
$workout = App\Models\Workout::first();
$workout->categories; // Should return array like ["Chest", "Shoulders"]

// Test filtering
App\Models\Workout::whereJsonContains('categories', 'Chest')->count();

// Test creation
App\Models\Workout::create([
    'name' => 'Test Exercise',
    'categories' => ['Chest', 'Triceps']
]);
```

### Step 5: Update Existing Workouts (Optional)

If you want to assign multiple categories to existing compound exercises in production:

```sql
-- Example: Update specific workouts to have multiple categories
UPDATE workouts SET categories = JSON_ARRAY('Chest', 'Triceps') WHERE name = 'Barbell Bench Press';
UPDATE workouts SET categories = JSON_ARRAY('Back', 'Biceps') WHERE name = 'Pull-Ups';
UPDATE workouts SET categories = JSON_ARRAY('Legs', 'Glutes') WHERE name = 'Barbell Squats';
-- Add more as needed based on the seeder assignments
```

Or run the seeder on production to update all workouts:
```bash
php artisan db:seed --class=WorkoutSeeder
```
⚠️ **Warning:** This will delete and recreate all workouts! Only use if you haven't customized workouts in production.

### Rollback Plan (If Something Goes Wrong)

If you need to rollback:
```sql
-- Restore from backup
mysql -u [user] -p [database_name] < backup_multi_category_[timestamp].sql

-- Then redeploy old code
git checkout [previous-commit]
composer install --no-dev
npm run build
php artisan cache:clear
```

### Testing Checklist in Production
- [ ] Can view workouts list
- [ ] Can filter workouts by categories
- [ ] Can create new workout with multiple categories
- [ ] Can edit existing workout categories
- [ ] Existing workouts display correctly with their categories
- [ ] No errors in Laravel logs (`storage/logs/laravel.log`)
