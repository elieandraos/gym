# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

```bash
# Frontend development
npm run dev              # Start Vite dev server
npm run build           # Build for production

# Backend development  
php artisan test        # Run all tests (using Pest)
./vendor/bin/pint       # Run Laravel Pint code formatter

# Database
php artisan migrate     # Run migrations
php artisan db:seed     # Run seeders
php artisan tinker      # Laravel REPL
```

## Architecture Overview

### Tech Stack
- **Backend**: Laravel 11 with Jetstream (without API, registration, 2FA, email verification)
- **Frontend**: Inertia.js + Vue 3 + Tailwind CSS 4
- **Testing**: Pest PHP testing framework
- **Database**: MySQL

### Domain Model
This is a gym management system centered around booking training sessions:

**Core Entities:**
- `User` - Members and trainers (role-based via `is_trainer` boolean for now)
- `Booking` - A scheduled training package (has multiple sessions)
- `BookingSlot` - Individual training session within a booking
- `Workout` - Exercise definition (categorized)
- `BookingSlotWorkout` - Workout performed in a session
- `BookingSlotWorkoutSet` - Individual sets within a workout (reps, weight, duration)

**Key Relationships:**
- Booking belongs to member and trainer
- Booking has many BookingSlots (sessions)
- BookingSlot has many BookingSlotWorkouts
- BookingSlotWorkout has many BookingSlotWorkoutSets

### Routing Patterns
All routes require authentication. Main patterns:
- `/members/*` - Member management
- `/trainers/*` - Trainer management  
- `/bookings/*` - Training package management
- `/bookings-slots/*` - Individual session management
- `/calendar` - Weekly calendar view

### Testing
Uses Pest with Feature tests covering:
- User management (members/trainers)
- Booking lifecycle (create, schedule, cancel)
- Workout management
- Calendar functionality
- Model scopes and business rules

### UI/UX Patterns
- Drag-and-drop workout selection
- Modal-based confirmations for destructive actions
- Form validation with error state preservation
- Responsive design with Tailwind CSS
- Inertia.js for SPA-like navigation without API complexity

## Coding Style Guide

### Laravel Backend Conventions

**Controllers:**
- Use single-purpose, action-specific controllers for complex operations (e.g., `ChangeBookingSlotDateTimeController`)
- Namespace admin controllers under `App\Http\Controllers\Admin`
- Use type-hinted parameters and return types
- Prefer eloquent `when()` over conditional queries
- Return `RedirectResponse` for form submissions with flash messages
- Use form requests for validation (`UserRequest`)
- Load relationships with explicit eager loading using `with()`

```php
// ✅ Good - use when() for conditional queries
$members = User::query()
    ->members()
    ->when(request('search'), function (Builder $query, string $search) {
        $query->where('name', 'like', "%$search%");
    })
    ->when(request()->has('activeTraining'), function (Builder $query) {
        if (request('activeTraining')) {
            $query->whereHas('memberActiveBooking');
        } else {
            $query->whereDoesntHave('memberActiveBooking');
        }
    })
    ->orderBy('registration_date', 'DESC')
    ->paginate(10);

// ✅ Good - explicit return types and flash messages
public function store(UserRequest $request): RedirectResponse
{
    // ... create logic
    return redirect()->route('admin.members.index')
        ->with('flash.banner', 'Member created successfully')
        ->with('flash.bannerStyle', 'success');
}
```

**Models:**
- Use PHP 8 attributes for scopes (`#[AsScope]`)
- Type-hint method parameters, especially Builder queries
- Use `HasMany`/`HasOne` return types for relationships
- Implement accessors with `Attribute::make()` using arrow functions
- Group related relationships together
- Use enum values consistently (`Role::Member->value`)

```php
// ✅ Good - Modern PHP 8 scope syntax
#[AsScope]
public function members(Builder $query): Builder
{
    return $query->where('role', '=', Role::Member->value);
}

// ✅ Good - Attribute accessors with arrow functions
public function age(): Attribute
{
    return Attribute::make(
        get: fn () => $this->birthdate
            ? Carbon::parse($this->birthdate)->diff(Carbon::now())->format('%y')
            : null,
    );
}
```

**Factories:**
- Use state methods for different scenarios (`active()`, `completed()`, `scheduled()`)
- Leverage realistic fake data that matches business logic
- Use Carbon for date manipulations
- Include probability-based booleans for realistic data

```php
// ✅ Good - State methods with descriptive names
public function active(): BookingFactory
{
    $startDate = $this->faker->dateTimeBetween('-20 days', '-5 days');
    $endDate = Carbon::instance($startDate)->addDays(30);

    return $this->state(function () use ($startDate, $endDate) {
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    });
}
```

**Database Seeders:**
- Keep seeders focused and call them in logical order from `DatabaseSeeder`
- Use factory states to create realistic test data
- Seed in order of dependencies (users before bookings)

### Vue/Inertia Frontend Conventions

**Component Structure:**
- Use `<script setup>` syntax consistently
- Import components in alphabetical order
- Use kebab-case for component file names
- Group imports: external libraries, then components, then composables

```vue
<!-- ✅ Good - Clean script setup with organized imports -->
<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    members: Object,
    search: { type: String, default: '' },
    activeTraining: { type: Boolean, default: true },
})
</script>
```

**Form Components:**
- Use `defineModel()` for two-way binding
- Implement `defineExpose()` for focus management
- Use consistent Tailwind classes for styling
- Include autofocus handling in `onMounted`

```vue
<!-- ✅ Good - Clean form component pattern -->
<template>
    <input
        ref="input"
        v-bind="$attrs"
        class="w-full p-2 border border-zinc-200 placeholder-zinc-400 hover:border-zinc-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm placeholder:font-normal"
        v-model="model"/>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const model = defineModel()
const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})

defineExpose({ focus: () => input.value.focus() })
</script>
```

**Component Organization:**
- Pages in `/resources/js/Pages/[Namespace]/` with index/show/create/edit pattern
- Reusable components in `/Components/Layout/` and `/Components/Form/`
- Page-specific partials in `/Pages/Admin/[Module]/Partials/`
- Use descriptive component names that indicate their purpose

**Search & Filtering:**
- Implement debounced search with `setTimeout`
- Use Inertia router for URL state management
- Preserve query strings with `withQueryString()`
- Handle filter state with reactive refs

### Testing Philosophy (Pest)

**Test Organization:**
- Use `describe()` blocks to group related functionality
- Test one concept per test method
- Use descriptive test names that explain the expected behavior
- Test both positive and negative scenarios
- Testing philosophy: test the request, assert the response and database changes.

```php
// ✅ Good - Descriptive test structure
describe('members scope', function () {
    it('returns only users with member role', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(1)->create(['role' => Role::Trainer->value]);

        $results = User::query()->members()->get();

        expect($results)->toHaveCount(2);
        expect($results->every(fn ($user) => $user->role === Role::Member->value))->toBeTrue();
    });
});
```

**Test Patterns:**
- Use seeded database in test setup - prefer using existing seeded data over factories
- Only create factories when no relevant seeded data exists, or update the seeder to include needed data
- Test model scopes and business logic in dedicated test files
- Use `expect()` assertions with fluent methods
- Test exception cases with `->throws()` syntax
- Focus on Feature tests over Unit tests for business logic

**Data Management in Tests:**
- Rely on seeded database state for consistent test data
- Use factories sparingly - only when seeded data doesn't cover the test scenario
- When adding new test scenarios, consider updating seeders rather than using factories
- Leverage existing relationships and data from seeders for more realistic testing

**Test Setup Philosophy:**
- Keep setup functions minimal - only create baseline data (users, basic bookings)
- Create test-specific data directly in each test using helper functions
- Avoid creating all possible data scenarios in setup - tests become slow and unclear
- Extract common test data creation into helper functions in `tests/Pest.php`

```php
// ✅ Good - Minimal setup + test-specific helper
function setupUsersAndBookings(): void
{
    // Only baseline: users, active bookings, completed bookings
}

function createExpiringBooking(User $member, User $trainer): Booking
{
    // Specific scenario: booking with 2 remaining sessions
}

test('it handles expiring bookings', function () {
    setupUsersAndBookings(); // Baseline
    $booking = createExpiringBooking($member, $trainer); // Test-specific
    // assertions...
});

// ❌ Avoid - Setup creates everything
function setupUsersAndBookings(): void
{
    // Creates: active, completed, expiring, frozen, unpaid bookings
    // Now ALL tests have ALL this data even if they don't need it
}
```

### Database Patterns

**Migrations:**
- Use descriptive migration names
- Include foreign key constraints
- Use appropriate column types for enums and dates

**Model Relationships:**
- Use descriptive relationship method names (`memberActiveBooking`, `trainerBookings`)
- Include necessary eager loading in relationship definitions
- Use query scopes for commonly filtered relationships

### General Guidelines

**Code Quality:**
- Use PHP 8+ features (attributes, arrow functions, named arguments)
- Prefer composition over inheritance
- Use meaningful variable and method names
- Type-hint all method parameters and return types
- Use enums for fixed value sets (Role, Gender, BloodType)

**Performance:**
- Eager load relationships to avoid N+1 queries
- Use `when()` for conditional queries to maintain query builder chain
- Implement pagination for list views
- Use database indexes for frequently queried columns

**Error Handling:**
- Use form requests for validation
- Return appropriate HTTP status codes
- Display user-friendly error messages
- Handle edge cases gracefully
- when adding new model field, update related seeder, factory, test setup db if applicable,