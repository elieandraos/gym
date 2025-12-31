# Implementation Plan: Trello-Style Circuit Workout UI

## Overview
Implement a Trello-like UI for managing booking slot circuit workouts on the booking slot show page. The interface will feature horizontal scrollable columns representing circuits, with each circuit containing workout cards. Users can add/remove circuits, add/remove workouts within circuits, and configure sets for each workout.

## User Requirements Summary
- Horizontal scrollable circuit columns (~30% width each)
- Column header (gray/zinc bg) with inline-editable circuit name
- Column body (gray/zinc bg) with "Add Workout" button at bottom
- Add Workout modal with searchable workout selection (like MembersSearch)
- Radio button for weight/duration type
- Dynamic sets: 3 by default with +/- buttons
  - Weight type: reps + weight fields
  - Duration type: duration field only
- Initial blank state with transparent "Add Circuit" button
- Use existing Modal component from Components/Layout

## Database Structure (Already Exists)
- BookingSlot → hasMany BookingSlotCircuit
- BookingSlotCircuit → hasMany BookingSlotCircuitWorkout
- BookingSlotCircuitWorkout → hasMany BookingSlotCircuitWorkoutSet
- BookingSlotCircuitWorkout → belongsTo Workout

All models, migrations, and resources already exist and are properly configured.

## Modal Component Location
The Modal component is located at `/resources/js/Components/Layout/Modal.vue` and is currently used in:
- `/resources/js/Components/Layout/Calendar/components/EventModal.vue` - Calendar event details
- `/resources/js/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue` - Logout confirmation

---

## Implementation Steps

### 1. Backend: Routes & Controllers

**File: `/routes/web.php`**
Add routes inside authenticated middleware group:
```php
Route::prefix('bookings-slots/{bookingSlot}')->name('admin.bookings-slots.')->group(function () {
    // Circuit management
    Route::post('/circuits', [BookingSlotCircuitsController::class, 'store'])->name('circuits.store');
    Route::patch('/circuits/{circuit}', [BookingSlotCircuitsController::class, 'update'])->name('circuits.update');
    Route::delete('/circuits/{circuit}', [BookingSlotCircuitsController::class, 'destroy'])->name('circuits.destroy');

    // Circuit Workout management
    Route::post('/circuits/{circuit}/workouts', [BookingSlotCircuitWorkoutsController::class, 'store'])->name('circuits.workouts.store');
    Route::delete('/circuits/{circuit}/workouts/{circuitWorkout}', [BookingSlotCircuitWorkoutsController::class, 'destroy'])->name('circuits.workouts.destroy');
});
```

**Create: `/app/Http/Controllers/Admin/BookingSlotCircuitsController.php`**
- `store()`: Create circuit with default name "Circuit 1", "Circuit 2", etc.
- `update()`: Update circuit name (inline editing)
- `destroy()`: Delete circuit (cascade delete workouts/sets)

**Create: `/app/Http/Controllers/Admin/BookingSlotCircuitWorkoutsController.php`**
- `store()`: Create workout with batch set creation
- `destroy()`: Delete workout (cascade delete sets)

**Validation Rules:**
- Circuit name: required, string, max 255
- Workout: workout_id (required, exists), type (required, in:weight,duration)
- Sets: array, min 1, max 10
- Set fields: reps (nullable, int, 1-999), weight_in_kg (nullable, numeric, 0-999), duration_in_seconds (nullable, int, 1-7200)

### 2. Backend: Data Loading

**Update: `/app/Http/Controllers/Admin/BookingSlotsController.php`**

In `show()` method, enhance eager loading:
```php
$bookingSlot->load([
    'booking',
    'booking.member',
    'booking.trainer',
    'circuits' => fn($query) => $query->orderBy('created_at'),
    'circuits.circuitWorkouts.workout',
    'circuits.circuitWorkouts.sets' => fn($query) => $query->orderBy('id'),
]);

$workouts = Workout::query()
    ->orderBy('name')
    ->get();

return Inertia::render('Admin/BookingsSlots/Show', [
    'bookingSlot' => BookingSlotResource::make($bookingSlot),
    'bookingId' => request('booking_id'),
    'workouts' => WorkoutResource::collection($workouts),
]);
```

**Update: `/app/Http/Resources/BookingSlotResource.php`**

Add circuits to resource transformation:
```php
'circuits' => $this->whenLoaded('circuits',
    fn() => BookingSlotCircuitResource::collection($this->circuits)
),
```

Note: BookingSlotCircuitResource already exists and properly transforms data.

### 3. Frontend: Main Page Component

**Update: `/resources/js/Pages/Admin/BookingsSlots/Show.vue`**

Transform from minimal header-only page to full Trello board:
- Add horizontal scrollable container below PageHeader
- Manage circuits state with reactive ref
- Handle circuit addition via events
- Display CircuitColumn components
- Show AddCircuitButton at the end

**Key responsibilities:**
- Maintain local circuits state array
- Handle circuit added/updated/deleted events
- Render circuit columns in horizontal scroll container

### 4. Frontend: Circuit Components

**Create: `/resources/js/Pages/Admin/BookingsSlots/Partials/CircuitColumn.vue`**
- Props: circuit (Object), bookingSlotId (Number)
- Emits: circuit-updated, circuit-deleted
- Structure: header + scrollable body with workout cards
- Manages local workout state
- Handles workout added/deleted events

**Create: `/resources/js/Pages/Admin/BookingsSlots/Partials/CircuitHeader.vue`**
- Props: circuit (Object), bookingSlotId (Number)
- Emits: updated, deleted
- Features:
  - Inline editable circuit name (click to edit, blur/Enter to save)
  - Delete circuit button with confirmation
  - Gray/zinc background styling

**Create: `/resources/js/Pages/Admin/BookingsSlots/Partials/CircuitWorkoutCard.vue`**
- Props: workout (Object), circuitId (Number), bookingSlotId (Number)
- Emits: deleted
- Display: workout name, category badge, sets summary
- White rounded box with hover effects
- Delete button with confirmation

**Create: `/resources/js/Pages/Admin/BookingsSlots/Partials/AddCircuitButton.vue`**
- Props: bookingSlotId (Number)
- Emits: circuit-added
- Transparent dashed border button
- POST to create circuit
- Show loading state during creation

**Create: `/resources/js/Pages/Admin/BookingsSlots/Partials/AddWorkoutButton.vue`**
- Props: circuitId (Number), bookingSlotId (Number)
- Opens AddWorkoutModal on click
- Simple button at bottom of circuit column

### 5. Frontend: Add Workout Modal

**Create: `/resources/js/Pages/Admin/BookingsSlots/Partials/AddWorkoutModal.vue`**
- Props: show (Boolean), circuitId (Number), bookingSlotId (Number), workouts (Array)
- Emits: close, workout-added
- Uses Modal component from `/resources/js/Components/Layout/Modal.vue`
- Uses InputAutocomplete from `/resources/js/Components/Form/InputAutocomplete.vue` for workout selection

**Form Fields:**
1. Workout selection: InputAutocomplete component
   - Transform WorkoutResource data ({ id, name, categories }) to InputAutocomplete format ({ value, label })
   - Computed property: `workoutOptions = workouts.map(w => ({ value: w.id, label: w.name }))`
   - Searchable, shows max 5 results
   - Required field

2. Type selection: Radio buttons
   - Options: "Weight" (default), "Duration"
   - Determines set fields shown

3. Sets section: Dynamic array (default 3 sets)
   - Add/remove buttons (+/- icons)
   - Weight type: Row with "Reps" + "Weight (kg)" textboxes
   - Duration type: Row with "Duration (seconds)" textbox
   - Display "Set 1", "Set 2", etc. labels

**Form Submission:**
- POST to `admin.bookings-slots.circuits.workouts.store`
- Payload: { workout_id, type, sets: [{ reps, weight_in_kg, duration_in_seconds }] }
- On success: emit workout-added event, close modal, reset form

**Form Validation:**
- Display inline errors with InputError component
- Disable submit button during processing

### 6. Styling Guidelines

**Horizontal Scroll Container:**
```vue
<div class="flex gap-4 overflow-x-auto pb-4 px-4">
  <!-- Circuit columns -->
</div>
```

**Circuit Column:**
```vue
<div class="flex flex-col w-[30%] min-w-[300px] flex-shrink-0">
  <!-- Header: detached -->
  <div class="bg-zinc-200 rounded-lg p-3">...</div>

  <!-- Body: gray bg with white cards -->
  <div class="bg-zinc-100 rounded-lg p-3 space-y-3 mt-2">
    <div class="bg-white rounded-lg p-3">...</div>
  </div>
</div>
```

**Add Circuit Button:**
```vue
<button class="w-[30%] min-w-[300px] flex-shrink-0 border-2 border-dashed border-zinc-300 rounded-lg p-6 text-zinc-400 hover:border-zinc-400 hover:text-zinc-500">
  Add Circuit
</button>
```

### 7. State Management Strategy

**Approach: Server-First with Local Updates**
1. User action triggers Inertia POST/PATCH/DELETE
2. Backend responds with success + flash message
3. Frontend updates local state via event handlers
4. No optimistic updates (simpler, consistent with codebase patterns)

**Benefits:**
- Single source of truth (backend)
- Proper validation handling
- No state drift
- Consistent with existing patterns

### 8. Testing Strategy

**Create: `/tests/Feature/Admin/BookingSlotCircuitTest.php`**

All circuit and circuit workout tests in one file:

**Circuit Tests:**
- Test circuit creation with default name
- Test circuit name update validation
- Test circuit deletion cascades to workouts/sets
- Test authentication requirement

**Circuit Workout Tests:**
- Test weight-based workout creation with 3 sets
- Test duration-based workout creation with 3 sets
- Test dynamic sets (1-10 sets)
- Test validation (workout_id exists, type in enum, sets array)
- Test workout deletion cascades to sets
- Test authentication requirement

**Test Pattern:**
```php
test('it creates a weight-based workout with sets', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
            ['reps' => 10, 'weight_in_kg' => 22.5, 'duration_in_seconds' => null],
            ['reps' => 8, 'weight_in_kg' => 25, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('booking_slot_circuit_workouts', [
        'booking_slot_circuit_id' => $circuit->id,
        'workout_id' => $workout->id,
    ]);

    $this->assertDatabaseCount('booking_slot_circuit_workout_sets', 3);
});
```

---

## Critical Files to Modify/Create

### Backend (Create New)
1. `/app/Http/Controllers/Admin/BookingSlotCircuitsController.php`
2. `/app/Http/Controllers/Admin/BookingSlotCircuitWorkoutsController.php`
3. `/routes/web.php` (add routes)

### Backend (Modify Existing)
4. `/app/Http/Controllers/Admin/BookingSlotsController.php` (update show method)
5. `/app/Http/Resources/BookingSlotResource.php` (add circuits)

### Frontend (Modify Existing)
6. `/resources/js/Pages/Admin/BookingsSlots/Show.vue` (major update)

### Frontend (Create New)
7. `/resources/js/Pages/Admin/BookingsSlots/Partials/CircuitColumn.vue`
8. `/resources/js/Pages/Admin/BookingsSlots/Partials/CircuitHeader.vue`
9. `/resources/js/Pages/Admin/BookingsSlots/Partials/CircuitWorkoutCard.vue`
10. `/resources/js/Pages/Admin/BookingsSlots/Partials/AddCircuitButton.vue`
11. `/resources/js/Pages/Admin/BookingsSlots/Partials/AddWorkoutButton.vue`
12. `/resources/js/Pages/Admin/BookingsSlots/Partials/AddWorkoutModal.vue`

### Tests (Create New)
13. `/tests/Feature/Admin/BookingSlotCircuitTest.php` (all circuit and workout tests)

---

## Implementation Sequence

### Phase 1: Backend Foundation
1. Create BookingSlotCircuitsController with store/update/destroy
2. Create BookingSlotCircuitWorkoutsController with store/destroy
3. Add routes to web.php
4. Update BookingSlotsController::show() to load circuits and workouts
5. Update BookingSlotResource to include circuits
6. Test with Postman/Tinker

### Phase 2: Basic UI Structure
7. Update Show.vue with horizontal scroll container and state management
8. Create AddCircuitButton component
9. Create CircuitColumn component skeleton
10. Create CircuitHeader with inline editing
11. Test circuit creation and name editing in browser

### Phase 3: Workout Management
12. Create CircuitWorkoutCard component
13. Create AddWorkoutButton component
14. Create AddWorkoutModal with searchable workout selection
15. Implement dynamic sets with add/remove buttons
16. Implement workout creation and deletion
17. Test weight and duration workouts in browser

### Phase 4: Testing & Polish
18. Write BookingSlotCircuitTest feature tests (circuits and workouts combined)
19. Add loading states and transitions
20. Refine styling and responsive behavior
21. Test edge cases (empty states, validation errors, long names)

---

## Potential Challenges & Solutions

**Challenge: Inline editing UX**
- Solution: Use transparent input styled as text, show border on focus, save on blur/Enter

**Challenge: Dynamic sets management**
- Solution: Use reactive array, splice for add/remove, start with 3 default sets

**Challenge: Modal form reset**
- Solution: Use Inertia's `form.reset()` in close handler and after successful submit

**Challenge: Workout search with categories**
- Solution: Use InputAutocomplete with custom slot for displaying categories as badges

**Challenge: Sets validation (different fields for weight vs duration)**
- Solution: Backend validates based on `type` field, frontend conditionally renders fields

---

## Future Enhancements (Out of Scope)
- Drag-and-drop reordering of circuits and workouts
- Circuit templates (save/load)
- Duplicate circuit functionality
- Edit existing sets inline
- Progress tracking across sessions
- Workout filtering by category in modal