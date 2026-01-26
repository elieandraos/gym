# Testing Guide

This document outlines the testing philosophy, structure, and patterns for Laravel applications using Pest

## Core Testing Philosophy

- **Prioritize feature tests over unit tests** - Feature tests provide the most value by testing the application as a whole
- **Test through HTTP** - Send requests, assert responses, check database state
- **Avoid excessive mocking** - Test with real database interactions
- **Use `RefreshDatabase`** for database isolation
- **Unit tests are the exception** - Only for pure functions without dependencies

## Test Suite Structure example
Organize tests by domain (what users interact with) rather than by code structure (controllers, models, etc.).

- Each test file focuses on one user-facing action or endpoint.
- Mirror the application's feature structure
```
tests/
├── Feature/                           # Feature tests (HTTP, workflows)
│   ├── {Domain}/                      # One folder per domain
│   │   ├── IndexTest.php              # List, search, filter
│   │   ├── ShowTest.php               # View single resource
│   │   ├── CreateTest.php             # Create flow
│   │   ├── UpdateTest.php             # Edit/update flow
│   │   ├── DeleteTest.php             # Delete flow
│   │   └── {SubFeature}/              # Sub-feature folder
│   │       └── IndexTest.php
│   └── ...
├── Console/                           # Artisan command tests
│   └── {CommandName}Test.php
├── Notifications/                     # Notification tests
│   ├── Email/
│   │   └── {NotificationName}Test.php
│   ├── Sms/
│   └── Push/
└── Unit/                              # Pure unit tests (minimal)
    └── {Class}Test.php
```

## When to Write Each Test Type

### Http Tests (majority)
- Testing controller actions
- Testing HTTP responses and redirects
- Testing validation errors
- Testing database state changes after requests

### Console Tests
- Testing Artisan commands
- Testing scheduled tasks

### Notifications Test
- Testing emails, SMS, push notifications being queued
- Testing notification content and recipients
- Testing notification delivery conditions (queued, sent)

### Unit Tests (minimal)
Only write unit tests when:
- Testing isolated utility functions or helpers
- Testing complex business logic decoupled from the framework
- Testing specific edge cases difficult to trigger through HTTP

## Test File Naming Conventions

- **Directory** indicates the domain: `Workouts/`, `Members/`, `Bookings/`
- **File name** indicates the action: `CreateTest.php`, `ShowTest.php`, `UpdateTest.php`
- **Test methods** describe the scenario: `test_authenticated_user_can_create_workout()`
- **Full path example**: `tests/Http/Workouts/CreateTest.php`

## Test Organization Rules

### Rule 1: One Action Per File
Each file should test a single user-facing action:
- `IndexTest.php` - List, search, filter operations
- `CreateTest.php` - Create flow (render form, store, validation, success page)
- `ShowTest.php` - View/display single resource
- `UpdateTest.php` - Edit/update flow (render form, update, validation)
- `DeleteTest.php` - Delete flow (confirmation, cascade)

### Rule 2: Use Subfolders for Sub-Features
When a domain has distinct sub-features with their own controllers, create subfolders that mirror the controller structure.

**✅ DO** - Sub-feature has its own controller → Create subfolder:
```
# FreezeBookingController → subfolder
tests/Http/Bookings/Freeze/CreateTest.php

# BodyCompositionController (nested under members/{member}) → subfolder
tests/Http/Members/BodyComposition/CreateTest.php

# CircuitController (nested under booking-slots/{slot}) → subfolder
tests/Http/BookingSlots/Circuits/CreateTest.php
```

**❌ DON'T** - Sub-feature as flat file:
```
tests/Http/Bookings/FreezeTest.php
tests/Http/Members/BodyCompositionTest.php
```

This keeps the parent folder clean and allows sub-features to have their own CRUD tests if needed.

### Rule 3: Mirror Controller Structure
Test organization should reflect the controller structure (Action + Domain pattern).

**✅ DO** - Same controller, multiple actions → Same folder, different files:
```
# TrainerController handles index, create, show, update
tests/Http/Trainers/IndexTest.php
tests/Http/Trainers/CreateTest.php
tests/Http/Trainers/ShowTest.php
tests/Http/Trainers/UpdateTest.php
```

**❌ DON'T** - All actions in one file:
```
tests/Http/Trainers/TrainerTest.php (with all CRUD tests inside)
```

**✅ DO** - Modal/inline actions handled by parent controller → Include in parent file:
```
# Profile photo upload is handled by TrainerController@update
tests/Http/Trainers/UpdateTest.php
  └── includes: photo upload tests, photo remove tests
```

**❌ DON'T** - Modal action as separate sub-feature:
```
tests/Http/Trainers/ProfilePhoto/UpdateTest.php
```

The key question is: **Does it have its own controller?**
- Yes → Subfolder
- No (same controller) → Same folder, action-based files

### Rule 4: Separate Notification Tests
Notifications tests live in the `Notifications/` test suite, not in `Http/`:
- `tests/Notifications/Emails/Members/WelcomeEmailTest.php`
- `tests/Notifications/Emails/Owners/NewMemberEmailTest.php`

- Don't duplicate email assertions in CRUD test files
- Group by recipient - Easy to find all emails a recipient receives

### Rule 5: Authentication Tests Per File
Each test file should have its own authentication test for the routes it covers:
```php
test('create routes require authentication', function () {
    $this->get(route('admin.members.create'))->assertRedirect(route('login'));
    $this->post(route('admin.members.store'))->assertRedirect(route('login'));
});
```

## Test Patterns

### HTTP Test Pattern

```php
beforeEach(function () {
    seedCommonData(); // Helper function defined in Pest.php
});

test('it requires authentication', function () {
    $this->get(route('route.name'))
        ->assertRedirect(route('login'));
});

test('it creates a resource successfully', function () {
    actingAsAuthenticatedUser() // Helper function defined in Pest.php
        ->post(route('route.name'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(...);

    $this->assertDatabaseHas(Model::class, [...]);
});

test('it validates required fields', function () {
    actingAsAuthenticatedUser()
        ->post(route('route.name'), [])
        ->assertSessionHasErrors(['field_name']);
});
```

### Model/Service Test Pattern

```php
describe('scope name', function () {
    it('returns expected results', function () {
        // Arrange
        Model::factory()->count(2)->create([...]);

        // Act
        $results = Model::query()->scopeName()->get();

        // Assert
        expect($results)->toHaveCount(2);
    });
});
```

## Data Setup Guidelines

- **Prefer seeded data to factories** for commonly used test data
- **Define helper functions in `Pest.php`** for common setup (seeding data, acting as authenticated user)
- **Use factories** only for test-specific scenarios that differ from seeded data

### Example helper functions in Pest.php

```php

function actingAsAdmin() {
    $admin = User::factory()->create(['role' => Role::Admin]);
    return test()->actingAs($admin);
}

function setupUsersAndBookings(): void {
    // Seed workouts, create members, trainers, bookings, etc.
}
```

## Inertia Assertions (Custom Macros)

Since we use Inertia.js, prefer custom macros over raw assertions:

| Macro                                                          | Purpose                    |
|----------------------------------------------------------------|----------------------------|
| `assertHasComponent('Component/Path')`                         | Check component rendered   |
| `assertHasProp('propName', $value)`                            | Check prop value           |
| `assertHasResource('propName', ResourceClass::class)`          | Check API resource         |
| `assertHasPaginatedResource('propName', ResourceClass::class)` | Check paginated collection |

## phpunit.xml Configuration

Configure four test suites in `phpunit.xml`:

```xml
<testsuites>
    <testsuite name="Http">
        <directory>tests/Http</directory>
    </testsuite>
    <testsuite name="Console">
        <directory>tests/Console</directory>
    </testsuite>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Notifications">
        <directory>tests/Notifications</directory>
    </testsuite>
</testsuites>
```

## Best Practices Summary

| Principle | Guideline |
|-----------|-----------|
| Test approach | Feature tests over unit tests (80/20 rule) |
| Test structure | HTTP request → Assert response → Assert database |
| Organization | Group by domain/feature, one action per file |
| Data setup | Use Pest.php helpers for common data, factories for specific |
| Database | Use RefreshDatabase, minimize mocking |
| Naming | Descriptive test methods that read like documentation |
| Assertions | Test behavior and outcomes, not implementation details |
| Refactoring | Tests should pass after refactoring if behavior is unchanged |

## Common Pitfalls to Avoid

- **Don't test framework behavior** - No need to test that Laravel's validation works
- **Don't over-mock** - Use real database interactions when possible
- **Don't test implementation details** - Test what users experience, not how code works internally
- **Don't duplicate tests** - One test file per action, avoid testing same behavior multiple times
- **Don't ignore test setup time** - If beforeEach is slow, consider what's truly necessary
- **Don't skip edge cases** - Null values, empty strings, boundary conditions matter
