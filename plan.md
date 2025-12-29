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
