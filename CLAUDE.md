# CLAUDE.md

This file provides guidance to Claude Code when working with this repository.

## Development Commands

```bash
npm run dev                    # Vite dev server
npm run build                  # Build for production
php artisan test               # Run tests (Pest)
php artisan test --filter=Name # Run specific tests
./vendor/bin/pint              # Code formatter
php artisan migrate            # Run migrations
php artisan db:seed            # Run seeders
```

## Architecture Overview

### Tech Stack
- **Backend**: Laravel 12 with Jetstream 5 (without API, registration, 2FA, email verification)
- **Frontend**: Inertia.js v2 + Vue 3 + Tailwind CSS 4
- **Testing**: Pest PHP 3
- **Database**: MySQL
- **Frontend Libraries**: Chart.js, date-fns, VueUse, Heroicons

### Domain Model
Gym management system for booking training sessions:

- `User` - Members and trainers (role-based via `Role` enum), includes profile, settings, and body metrics
- `Booking` - Training package assigned to member with trainer (has schedule days, payment info, freeze support)
- `BookingSlot` - Individual training session with status tracking
- `BookingSlotCircuit` - Named circuit grouping within a session (e.g., "Upper Body", "Circuit 1")
- `BookingSlotCircuitWorkout` - Workout assigned to a circuit
- `BookingSlotCircuitWorkoutSet` - Individual set (reps, weight, duration)
- `Workout` - Exercise definition
- `BodyComposition` - Member progress photos over time

### Routing Patterns
All routes require authentication: `/members/*`, `/trainers/*`, `/bookings/*`, `/bookings-slots/*`, `/calendar`

## Coding Style Guide

### Laravel Backend

**Controllers:**
- Single-purpose controllers for complex operations
- Namespace under `App\Http\Controllers\Admin`
- Type-hinted parameters and return types
- Use `when()` for conditional queries
- Use form requests for validation
- Explicit eager loading with `with()`
- `index()` and `show()` methods must always return API Resources (never raw models)

** PHPStorm warnings **
- For unused route model binding parameters (needed for nested routes), add `/** @noinspection PhpUnusedParameterInspection */` above the method
- Use `/** @var ModelClass $variable */` to specify types and avoid polymorphic call warnings for: `auth()->user()`, `request()->user()`, relationship `create()` methods, `Collection::first()` / `firstWhere()`
- Use `Model::query()->orderBy()` instead of `Model::orderBy()` to avoid "method not found" warnings in PhpStorm
- For scope methods, separate the query into a typed variable: `/** @var Builder|Booking $query */ $query = Booking::query();` then call scopes on `$query`
- When a scope calls another scope inside the model, use `/** @noinspection PhpUndefinedMethodInspection - Scope calling another scope, PhpStorm can't recognize it */`
- When a relationship calls a scope from the related model, use `/** @noinspection PhpUndefinedMethodInspection - Relationship calling Model scope, PhpStorm can't recognize it */`
- Avoid unnecessary curly braces in string interpolation for simple property access (use `"$user->id"` not `"{$user->id}"`)

**Models:**
- Use `#[AsScope]` attribute for scopes
- Type-hint Builder queries
- Use `Attribute::make()` for accessors
- Use enum values consistently
- Add `@property` PHPDoc annotations for model attributes, and `@property-read` for relationships, to avoid "Property accessed via magic method" warnings in PhpStorm
- Add `@method static Builder|ModelClass scopeName()` annotations for scopes to enable autocomplete
- Add `@mixin TraitName` in model PHPDoc when using traits for autocomplete and go-to-definition support

### API Resources (CRITICAL)

**Golden Rules:**
1. Resources CAN serialize **downward** relationships (parent → children)
2. Resources CAN serialize **reference** relationships (belongsTo)
3. Resources must **NEVER** serialize inverse/parent relationships
4. Resources must **NEVER** access loaded relationships for calculations
5. Models must **NEVER** auto-eager load relationships

**Why:** Circular references cause 502 errors in production (infinite recursion).

**PHPDoc:** Add `@mixin ModelClass` to resources to avoid "property accessed via magic method" warnings.

**Allowed Relationship Map:**
```php
BookingResource {
    'member' => MemberResource,              // belongsTo ✅
    'trainer' => TrainerResource,            // belongsTo ✅
    'bookingSlots' => BookingSlotResource[], // hasMany ✅
}

BookingSlotResource {
    'circuits' => [...], // hasMany ✅
    // NO 'booking' ❌
}

MemberResource / TrainerResource {
    // Profile data only
    // NO 'bookings' ❌
}
```

**Controller Patterns:**
- Downward navigation: Pass nested data in single prop
- Upward navigation: Use separate props (MemberResource can't include bookings)

### Vue/Inertia Conventions

**Component Structure:**
- Use `<script setup>` syntax
- Import order: external libs → components → composables
- Pages in `/Pages/[Namespace]/` with index/show/create/edit pattern
- Partials in `/Pages/Admin/[Module]/Partials/`

**Key Rules:**
- Keep templates flat - extract complex sections into Partials
- Components own their UI (dropdowns, icons inside component)
- Use props down, events up
- Name by domain purpose (`MembersFilters` not `FilterDropdown`)
- Use Ziggy's `route().current()` for active states, never URL comparison

**Reactivity:**
- Don't destructure props outside computed/watch if used reactively
- Use `props.value` inside computed functions

### Testing (Pest)

Prioritize feature tests over unit tests. Test through HTTP requests, assert responses and database state. Avoid excessive mocking - use real database with RefreshDatabase. Tests verify behavior, not implementation.

See [TESTING.md](learnings/TESTING.md) for comprehensive testing documentation.

**Quick Reference:**
- Run tests: `php artisan test`
- Run specific: `php artisan test --filter=TestName`
- Prefer custom Inertia macros: `assertHasComponent`, `assertHasProp`, `assertHasResource`, `assertHasPaginatedResource`
- Chain consecutive `expect()` calls using `->and()` instead of separate statements:
  ```php
  // Good
  expect($booking)->not->toBeNull()
      ->and($booking->id)->toBe(1)
      ->and($booking->name)->toBe('Test');

  // Bad
  expect($booking)->not->toBeNull();
  expect($booking->id)->toBe(1);
  expect($booking->name)->toBe('Test');
  ```
- For "Unhandled JsonException" warnings on assertions like `assertSessionHasNoErrors()`, add file-level suppression: `/** @noinspection PhpUnhandledExceptionInspection */`
- For "Potentially polymorphic call" warnings on `factory()->create()` results (returns `Model|ClassName`), add type hint before the assignment: `/** @var User $member */`

### General Guidelines

- Use PHP 8+ features (attributes, arrow functions, enums)
- Type-hint all parameters and return types
- Eager load to avoid N+1
- Use form requests for validation
- When adding model fields, update migrations/seeder/factory/tests. for migrations, ask if still in development mode. if dev mode, just update the initial migration and ask to run db refresh. if in production, also ask if prefer to create a new migration file or update the original and handle manually on production (give instructions like sql statements to execute)
- When feature/task is not working over 3 iterations, ask for debugging info
- Always import namespaces at the top of files, never use inline fully qualified class names (e.g., `use Collection;` not `new \Illuminate\Support\Collection()`)

## Session Usage Optimization

- Run targeted tests (`--filter=SpecificTest`) instead of full suite
- Only run tests when explicitly asked
- Ask before running full test suite
