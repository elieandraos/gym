# Codebase Audit: Laravel Boost Guidelines Compliance

**Generated**: February 2, 2026
**Total Violations**: 55

---

## Summary

| Category | Violations | Status |
|----------|------------|--------|
| PHP Closure Type Hints | 22 | ❌ |
| Laravel Form Requests | 4 | ⚠️ |
| Testing Conventions | 0 | ✅ |
| Vue.js PascalCase | 27 | ❌ |
| Inertia.js Conventions | 0 | ✅ |
| Database Files | 2 | ⚠️ |

---

## High Priority Fixes

### 1. PHP Closure Type Hints (22 violations)

All closures, especially query builders, must have type hints on their parameters.

#### Controllers

- [ ] **app/Http/Controllers/Admin/BookingsController.php:74**
  ```php
  // Before
  ->load(['member', 'trainer', 'bookingSlots' => function ($query) {

  // After
  use Illuminate\Database\Eloquent\Builder;
  ->load(['member', 'trainer', 'bookingSlots' => function (Builder $query) {
  ```

- [ ] **app/Http/Controllers/Admin/WeeklyCalendarController.php:52**
  ```php
  // Before
  ->when(! empty($selectedTrainerIds), function ($query) use ($selectedTrainerIds) {

  // After
  ->when(! empty($selectedTrainerIds), function (Builder $query) use ($selectedTrainerIds) {
  ```

- [ ] **app/Http/Controllers/Admin/DailyCalendarController.php:41**
  ```php
  // Before
  ->when(! empty($selectedTrainerIds), function ($q) use ($selectedTrainerIds) {

  // After
  ->when(! empty($selectedTrainerIds), function (Builder $q) use ($selectedTrainerIds) {
  ```

- [ ] **app/Http/Controllers/Admin/DailyCalendarController.php:47**
  ```php
  // Before
  ->filter(function ($slot) use ($startOfDay, $endOfDay) {

  // After
  ->filter(function (mixed $slot) use ($startOfDay, $endOfDay) {
  ```

- [ ] **app/Http/Controllers/Admin/BookingSlotsController.php:21**
  ```php
  // Before
  'circuits' => fn ($query) => $query->orderBy('created_at'),

  // After
  'circuits' => fn (Builder $query) => $query->orderBy('created_at'),
  ```

- [ ] **app/Http/Controllers/Admin/BookingSlotsController.php:23**
  ```php
  // Before
  'circuits.circuitWorkouts.sets' => fn ($query) => $query->orderBy('id'),

  // After
  'circuits.circuitWorkouts.sets' => fn (Builder $query) => $query->orderBy('id'),
  ```

- [ ] **app/Http/Controllers/Admin/UnfreezeBookingController.php:23**
  ```php
  // Before
  'bookingSlots' => function ($query) {

  // After
  'bookingSlots' => function (Builder $query) {
  ```

- [ ] **app/Http/Controllers/Admin/MembersController.php:57**
  ```php
  // Before
  'memberActiveBooking.bookingSlots' => function ($query) {

  // After
  'memberActiveBooking.bookingSlots' => function (Builder $query) {
  ```

- [ ] **app/Http/Controllers/DashboardController.php:20**
  ```php
  // Before
  ->where(function ($query) {

  // After
  ->where(function (Builder $query) {
  ```

- [ ] **app/Http/Controllers/DashboardController.php:40**
  ```php
  // Before
  ->filter(function ($booking) {

  // After
  use App\Models\Booking;
  ->filter(function (Booking $booking) {
  ```

- [ ] **app/Http/Controllers/DashboardController.php:72**
  ```php
  // Before
  ->whereHas('memberBookings', function ($query) use ($thirtyDaysAgo) {

  // After
  ->whereHas('memberBookings', function (Builder $query) use ($thirtyDaysAgo) {
  ```

- [ ] **app/Http/Controllers/DashboardController.php:98**
  ```php
  // Before
  ->map(function ($trainer) {

  // After
  use App\Models\User;
  ->map(function (User $trainer) {
  ```

- [ ] **app/Http/Controllers/DashboardController.php:113**
  ```php
  // Before
  $expiringBookings->map(function ($booking) {

  // After
  $expiringBookings->map(function (Booking $booking) {
  ```

#### Models

- [ ] **app/Models/BookingSlot.php:69**
  ```php
  // Before
  return $query->where(function ($q) use ($start, $end) {

  // After
  return $query->where(function (Builder $q) use ($start, $end) {
  ```

- [ ] **app/Models/Booking.php:122**
  ```php
  // Before
  ->where(function ($q) use ($start, $end) {

  // After
  ->where(function (Builder $q) use ($start, $end) {
  ```

- [ ] **app/Models/User.php:191**
  ```php
  // Before
  ->when($role === Role::Member->value, function ($query) {

  // After
  ->when($role === Role::Member->value, function (Builder $query) {
  ```

#### Resources

- [ ] **app/Http/Resources/Calendar/WeekEventsCollection.php:13**
  ```php
  // Before
  ->map(function ($eventArray) {

  // After
  ->map(function (mixed $eventArray) {
  ```

- [ ] **app/Http/Resources/Calendar/DayEventsCollection.php:13**
  ```php
  // Before
  ->map(function ($eventArray) {

  // After
  ->map(function (mixed $eventArray) {
  ```

#### Requests & Rules

- [ ] **app/Http/Requests/Admin/UserSettingsRequest.php:30**
  ```php
  // Before
  function ($attribute, $value, $fail) {

  // After
  use Closure;
  function (string $attribute, mixed $value, Closure $fail) {
  ```

- [ ] **app/Rules/DoesNotOverlapWithOtherMemberBookings.php:36**
  ```php
  // Before
  ->where(function ($query) use ($startDate, $endDate) {

  // After
  ->where(function (Builder $query) use ($startDate, $endDate) {
  ```

#### Services

- [ ] **app/Services/BookingManager.php:55**
  ```php
  // Before
  $schedule = array_map(function ($dayTime) {

  // After
  $schedule = array_map(function (array $dayTime) {
  ```

---

### 2. Vue.js PascalCase Component Names (27 violations)

All Vue component names in templates must use PascalCase.

#### Pages/Admin/DailyCalendar/Index.vue

- [ ] **Line 5**: `<daily-calendar>` → `<DailyCalendar>`

#### Pages/Admin/Calendar/Index.vue

- [ ] **Line 5**: `<weekly-calendar>` → `<WeeklyCalendar>`

#### Pages/Admin/Workouts/Index.vue

- [ ] **Line 6**: `<workouts-search>` → `<WorkoutsSearch>`
- [ ] **Line 12**: `<workouts-filters>` → `<WorkoutsFilters>`

#### Pages/Admin/Members/Index.vue

- [ ] **Line 6**: `<members-search>` → `<MembersSearch>`
- [ ] **Line 12**: `<members-filters>` → `<MembersFilters>`

#### Pages/Admin/Members/PersonalInfo.vue

- [ ] **Line 9**: `<user-profile>` → `<UserProfile>`
- [ ] **Line 10**: `<user-contact>` → `<UserContact>`

#### Pages/Admin/Trainers/Index.vue

- [ ] **Line 4**: `<page-header>` → `<PageHeader>`
- [ ] **Line 6**: `<trainers-search>` → `<TrainersSearch>`
- [ ] **Line 19**: `<trainers-list>` → `<TrainersList>`

#### Pages/Admin/Trainers/Show.vue

- [ ] **Line 9**: `<user-profile>` → `<UserProfile>`
- [ ] **Line 10**: `<user-contact>` → `<UserContact>`

#### Pages/Admin/Bookings/Create.vue

- [ ] **Line 65**: `<booking-schedule>` → `<BookingSchedule>`
- [ ] **Line 69**: `<primary-button>` → `<PrimaryButton>`

#### Pages/Admin/BookingsSlots/Edit.vue

- [ ] **Line 4**: `<page-header>` → `<PageHeader>`
- [ ] **Line 22**: `<primary-button>` → `<PrimaryButton>`

#### Pages/Admin/ChangeBookingSlotDateTime/Edit.vue

- [ ] **Line 22**: `<primary-button>` → `<PrimaryButton>`

#### Pages/Admin/FreezeBooking/Index.vue

- [ ] **Line 12**: `<primary-button>` → `<PrimaryButton>`

#### Pages/Admin/UnfreezeBooking/Index.vue

- [ ] **Line 20**: `<primary-button>` → `<PrimaryButton>`

#### Pages/Admin/CancelBookingSlot/Index.vue

- [ ] **Line 12**: `<primary-button>` → `<PrimaryButton>`

#### Pages/Admin/Bookings/Partials/BookingSchedule.vue

- [ ] **Line 9**: `<repeatable-scheduler>` → `<RepeatableScheduler>`

#### Pages/Admin/Bookings/Partials/RepeatableScheduler.vue

- [ ] **Line 10**: `<x-circle-icon>` → `<XCircleIcon>`

#### Components/Form/InputAutocomplete.vue

- [ ] **Line 12**: `<x-mark-icon>` → `<XMarkIcon>`

#### Pages/Admin/Members/Partials/MembersSearch.vue

- [ ] **Line 3**: `<text-input>` → `<TextInput>`

#### Pages/Admin/Workouts/Partials/WorkoutsSearch.vue

- [ ] **Line 3**: `<text-input>` → `<TextInput>`

#### Pages/Admin/Trainers/Partials/TrainersSearch.vue

- [ ] **Line 3**: `<text-input>` → `<TextInput>`

---

## Medium Priority Fixes

### 3. Create Form Request Classes (4 violations)

Controllers should use Form Request classes instead of inline validation.

- [ ] **app/Http/Controllers/Admin/BookingSlotCircuitWorkoutsController.php:17-24**

  Create `app/Http/Requests/Admin/StoreBookingSlotCircuitWorkoutRequest.php`:
  ```php
  <?php

  namespace App\Http\Requests\Admin;

  use Illuminate\Foundation\Http\FormRequest;

  class StoreBookingSlotCircuitWorkoutRequest extends FormRequest
  {
      public function authorize(): bool
      {
          return true;
      }

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
  }
  ```

- [ ] **app/Http/Controllers/Admin/BookingSlotCircuitWorkoutsController.php:48-55**

  Create `app/Http/Requests/Admin/UpdateBookingSlotCircuitWorkoutRequest.php`

- [ ] **app/Http/Controllers/Admin/BookingSlotCircuitsController.php:15-17**

  Create `app/Http/Requests/Admin/StoreBookingSlotCircuitRequest.php`:
  ```php
  <?php

  namespace App\Http\Requests\Admin;

  use Illuminate\Foundation\Http\FormRequest;

  class StoreBookingSlotCircuitRequest extends FormRequest
  {
      public function authorize(): bool
      {
          return true;
      }

      public function rules(): array
      {
          return [
              'name' => 'nullable|string|max:255',
          ];
      }
  }
  ```

- [ ] **app/Http/Controllers/Admin/BookingSlotCircuitsController.php:35-37**

  Create `app/Http/Requests/Admin/UpdateBookingSlotCircuitRequest.php`

---

### 4. Database Seeder Closure Type Hints (2 violations)

- [ ] **database/seeders/UserSeeder.php:24**
  ```php
  // Before
  $members->each(function ($user) use ($trainers)

  // After
  use App\Models\User;
  $members->each(function (User $user) use ($trainers)
  ```

- [ ] **database/seeders/BookingSeeder.php:36**
  ```php
  // Before
  $members->each(function ($user, $index) use ($trainers)

  // After
  use App\Models\User;
  $members->each(function (User $user, int $index) use ($trainers)
  ```

---

## Low Priority Fixes

### 5. Standardize Faker Usage in Factories

Consider standardizing faker usage across all factories. Currently:
- `UserFactory.php` uses `fake()`
- `BookingFactory.php` uses `$this->faker`
- `BookingSlotFactory.php` uses `$this->faker`

**Recommendation**: Choose one approach and apply it consistently.

---

## Compliant Areas (No Action Required)

- ✅ All tests use Pest syntax correctly (54 test files)
- ✅ All tests use factories appropriately
- ✅ Factory states properly utilized
- ✅ No `env()` usage outside config files
- ✅ No `DB::` facade usage (uses Eloquent)
- ✅ Proper use of named routes with `route()`
- ✅ Controllers properly use `Inertia::render()`
- ✅ Vue pages properly organized in `resources/js/Pages/`
- ✅ No semantic HTML violations (no `<a href="#" @click.prevent>` patterns)
- ✅ All migrations have proper return types
- ✅ Laravel 12 structure followed correctly
- ✅ Curly braces on all control structures
- ✅ PHP 8 constructor property promotion used
- ✅ No empty constructors
- ✅ Explicit return types on methods
- ✅ Eloquent relationships have return type hints
- ✅ Eager loading implemented (no N+1 issues)
- ✅ Vue single root elements
- ✅ Vue `<script setup>` syntax

---

## How to Use This File

1. Work through each section in priority order (High → Medium → Low)
2. Check off items as you complete them using `[x]`
3. Run `vendor/bin/pint --dirty` after PHP changes
4. Run tests after each batch of fixes: `php artisan test --compact`
5. Delete this file when all fixes are complete