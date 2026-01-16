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
- **Backend**: Laravel 11 with Jetstream (without API, registration, 2FA, email verification)
- **Frontend**: Inertia.js + Vue 3 + Tailwind CSS 4
- **Testing**: Pest PHP
- **Database**: MySQL

### Domain Model
Gym management system for booking training sessions:

- `User` - Members and trainers (role-based)
- `Booking` - Training package (has multiple sessions)
- `BookingSlot` - Individual training session
- `Workout` - Exercise definition
- `BookingSlotWorkout` / `BookingSlotWorkoutSet` - Workout tracking

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

** PHPStorm warnings **
- For unused route model binding parameters (needed for nested routes), add `/** @noinspection PhpUnusedParameterInspection */` above the method
- Use `/** @var ModelClass $variable */` to specify types and avoid polymorphic call warnings for: `auth()->user()`, `request()->user()`, relationship `create()` methods, `Collection::first()` / `firstWhere()`
- Use `Model::query()->orderBy()` instead of `Model::orderBy()` to avoid "method not found" warnings in PhpStorm
- Avoid unnecessary curly braces in string interpolation for simple property access (use `"$user->id"` not `"{$user->id}"`)

**Models:**
- Use `#[AsScope]` attribute for scopes
- Type-hint Builder queries
- Use `Attribute::make()` for accessors
- Use enum values consistently
- Add `@property` PHPDoc annotations for model attributes, and `@property-read` for relationships, to avoid "Property accessed via magic method" warnings in PhpStorm

### API Resources (CRITICAL)

**Golden Rules:**
1. Resources CAN serialize **downward** relationships (parent → children)
2. Resources CAN serialize **reference** relationships (belongsTo)
3. Resources must **NEVER** serialize inverse/parent relationships
4. Resources must **NEVER** access loaded relationships for calculations
5. Models must **NEVER** auto-eager load relationships

**Why:** Circular references cause 502 errors in production (infinite recursion).

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

**Organization:**
- `describe()` blocks for grouping
- Descriptive test names
- Test request → assert response + database changes

**Patterns:**
- Prefer seeded data over factories
- Use Inertia's `assertInertia()` assertions
- Use custom macros: `assertHasComponent`, `assertHasProp`, `assertHasResource`, `assertHasPaginatedResource`
- Minimal setup + test-specific helpers

### General Guidelines

- Use PHP 8+ features (attributes, arrow functions, enums)
- Type-hint all parameters and return types
- Eager load to avoid N+1
- Use form requests for validation
- When adding model fields, update seeder/factory/tests
- When feature/task is not working over 3 iterations, ask for debugging info
- Always import namespaces at the top of files, never use inline fully qualified class names (e.g., `use Collection;` not `new \Illuminate\Support\Collection()`)

## Session Usage Optimization

- Run targeted tests (`--filter=SpecificTest`) instead of full suite
- Only run tests when explicitly asked
- Ask before running full test suite
