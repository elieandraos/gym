# Laravel Boost Guidelines Audit Report

**Generated**: January 31, 2026
**Total Violations**: 45

---

## High Priority - Form Request Classes

These controllers use inline validation instead of dedicated Form Request classes.

### BookingSlotCircuitsController

- [ ] **File**: `app/Http/Controllers/Admin/BookingSlotCircuitsController.php`

**Lines 15 and 35** - Create `StoreBookingSlotCircuitRequest` and `UpdateBookingSlotCircuitRequest`

**Before** (line 15):
```php
public function store(Request $request, BookingSlot $bookingSlot): RedirectResponse
{
    $request->validate([
        'name' => 'nullable|string|max:255',
    ]);
    // ...
}
```

**After**:
```php
// app/Http/Requests/StoreBookingSlotCircuitRequest.php
public function rules(): array
{
    return [
        'name' => 'nullable|string|max:255',
    ];
}

// Controller
public function store(StoreBookingSlotCircuitRequest $request, BookingSlot $bookingSlot): RedirectResponse
{
    // validation handled by Form Request
}
```

### BookingSlotCircuitWorkoutsController

- [ ] **File**: `app/Http/Controllers/Admin/BookingSlotCircuitWorkoutsController.php`

**Lines 17 and 48** - Create `StoreBookingSlotCircuitWorkoutRequest` and `UpdateBookingSlotCircuitWorkoutRequest`

**Before** (line 17):
```php
$request->validate([
    'workout_id' => 'required|exists:workouts,id',
    'notes' => 'nullable|string|max:255',
    'sets' => 'required|array|min:1',
    'sets.*.reps' => 'required|integer|min:1',
    'sets.*.weight' => 'required|numeric|min:0',
]);
```

**After**:
```php
// app/Http/Requests/StoreBookingSlotCircuitWorkoutRequest.php
public function rules(): array
{
    return [
        'workout_id' => 'required|exists:workouts,id',
        'notes' => 'nullable|string|max:255',
        'sets' => 'required|array|min:1',
        'sets.*.reps' => 'required|integer|min:1',
        'sets.*.weight' => 'required|numeric|min:0',
    ];
}
```

---

## Medium Priority - PHP Type Hints

Add type hints to closure parameters throughout the codebase.

### Services

- [ ] **File**: `app/Services/BookingManager.php`
  - **Line 55**: Add `array` type hint
  ```php
  // Before
  array_map(function ($dayTime) {
  // After
  array_map(function (array $dayTime) {
  ```

### Rules

- [ ] **File**: `app/Rules/DoesNotOverlapWithOtherMemberBookings.php`
  - **Line 36**: Add `Builder` type hint
  ```php
  // Before
  ->where(function ($query) use ($startDate, $endDate) {
  // After
  use Illuminate\Database\Eloquent\Builder;
  ->where(function (Builder $query) use ($startDate, $endDate) {
  ```

### Models

- [ ] **File**: `app/Models/Booking.php`
  - **Line 122**: Add `Builder` type hint to `$q`

- [ ] **File**: `app/Models/BookingSlot.php`
  - **Line 69**: Add `Builder` type hint to `$q`

- [ ] **File**: `app/Models/User.php`
  - **Line 191**: Add `Builder` type hint to `$query`
  - **Line 193**: Add `Builder` type hint to `$query`
  - **Line 216**: Add `?string` type hint to `$previous`

### Controllers

- [ ] **File**: `app/Http/Controllers/DashboardController.php`
  - **Line 20**: Add `Builder` type hint to `$query`
  - **Line 40**: Add `Booking` type hint to `$booking`
  - **Line 72**: Add `Builder` type hint to `$query`
  - **Line 98**: Add `User` type hint to `$trainer`
  - **Line 113**: Add `Booking` type hint to `$booking`

- [ ] **File**: `app/Http/Controllers/Admin/BookingsController.php`
  - **Line 74**: Add `Builder` type hint to `$query`

- [ ] **File**: `app/Http/Controllers/Admin/DailyCalendarController.php`
  - **Line 41**: Add `Builder` type hint to `$q`
  - **Line 47**: Add `BookingSlot` type hint to `$slot`

- [ ] **File**: `app/Http/Controllers/Admin/WeeklyCalendarController.php`
  - **Line 52**: Add `Builder` type hint to `$query`

- [ ] **File**: `app/Http/Controllers/Admin/MembersController.php`
  - **Line 57**: Add `Builder` type hint to `$query`

- [ ] **File**: `app/Http/Controllers/Admin/UnfreezeBookingController.php`
  - **Line 23**: Add `Builder` type hint to `$query`

- [ ] **File**: `app/Http/Controllers/Admin/BookingSlotsController.php`
  - **Line 21**: Add `Builder` type hint in arrow function
  - **Line 23**: Add `Builder` type hint in arrow function

- [ ] **File**: `app/Http/Controllers/Admin/BookingSlotCircuitWorkoutHistoryController.php`
  - **Line 26**: Add `Builder` type hint
  - **Line 28**: Add `Builder` type hint

- [ ] **File**: `app/Http/Controllers/Admin/UserSettingsController.php`
  - **Line 20**: Add `User` type hint to `$trainer`

### Resources

- [ ] **File**: `app/Http/Resources/Calendar/WeekEventsCollection.php`
  - **Line 13**: Add type hint to `$eventArray`

- [ ] **File**: `app/Http/Resources/Calendar/DayEventsCollection.php`
  - **Line 13**: Add type hint to `$eventArray`

---

## Medium Priority - Vue.js PascalCase Components

Convert kebab-case component usage to PascalCase in Vue templates.

### Members Pages

- [ ] **File**: `resources/js/Pages/Admin/Members/Index.vue`
  - **Line 6**: Change `<members-search>` to `<MembersSearch>`
  - **Line 12**: Change `<members-filters>` to `<MembersFilters>`

- [ ] **File**: `resources/js/Pages/Admin/Members/Partials/MembersSearch.vue`
  - **Line 3**: Change `<text-input>` to `<TextInput>`

- [ ] **File**: `resources/js/Pages/Admin/Members/Partials/MembersFilters.vue`
  - **Line 2**: Change `<dropdown>` to `<Dropdown>`

### Trainers Pages

- [ ] **File**: `resources/js/Pages/Admin/Trainers/Index.vue`
  - **Line 4**: Change `<page-header>` to `<PageHeader>`
  - **Line 6**: Change `<trainers-search>` to `<TrainersSearch>`
  - **Line 12**: Change `<primary-button>` to `<PrimaryButton>`
  - **Line 19**: Change `<trainers-list>` to `<TrainersList>`

### Workouts Pages

- [ ] **File**: `resources/js/Pages/Admin/Workouts/Index.vue`
  - **Line 6**: Change `<workouts-search>` to `<WorkoutsSearch>`
  - **Line 12**: Change `<workouts-filters>` to `<WorkoutsFilters>`

### Layout

- [ ] **File**: `resources/js/Layouts/AppLayout.vue`
  - **Line 10**: Change `<sidebar>` to `<Sidebar>`

### Components

- [ ] **File**: `resources/js/Components/Form/InputAutocomplete.vue`
  - **Line 12**: Change `<x-mark-icon>` to `<XMarkIcon>`

---

## Low Priority - Semantic HTML

Replace `<a href="#">` with proper button elements.

- [ ] **File**: `resources/js/Pages/Admin/Members/Partials/PaymentStatusWidget.vue`
  - **Lines 17-23**: Replace anchor with button
  ```vue
  <!-- Before -->
  <a href="#" @click.prevent="markAsPaid" class="...">
      Mark as paid
  </a>

  <!-- After -->
  <button type="button" @click="markAsPaid" class="...">
      Mark as paid
  </button>
  ```

- [ ] **File**: `resources/js/Pages/Admin/Dashboard/Partials/UnpaidCard.vue`
  - **Lines 37-43**: Replace anchor with button
  ```vue
  <!-- Before -->
  <a href="#" @click.prevent="markAsPaid(member)" class="...">
      Mark as paid
  </a>

  <!-- After -->
  <button type="button" @click="markAsPaid(member)" class="...">
      Mark as paid
  </button>
  ```

---

## Summary

| Priority | Category | Count | Status |
|----------|----------|-------|--------|
| High | Form Request Classes | 4 methods | Pending |
| Medium | PHP Type Hints | 27 instances | Pending |
| Medium | Vue PascalCase | 12 instances | Pending |
| Low | Semantic HTML | 2 instances | Pending |
| **Total** | | **45** | |

---

## Compliant Areas

The following areas fully comply with Laravel Boost guidelines:

- Curly braces on control structures
- PHP 8 constructor property promotion
- No empty constructors
- Return type declarations on all methods
- Enum TitleCase keys
- PHPDoc block usage
- Eloquent relationship return type hints
- No DB:: facade usage (uses Model::query())
- No env() calls outside config files
- Proper eager loading (no N+1 issues)
- Pest testing syntax (all 54 tests)
- Factory usage in tests
- Test directory structure
- Vue single root elements
- Vue `<script setup>` syntax
- Vue props definitions
- Inertia useForm usage (59 forms)
- Inertia router usage
- Inertia::render in controllers
