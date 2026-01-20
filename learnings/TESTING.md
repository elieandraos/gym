# Testing Guide

This document outlines the testing philosophy, structure, and patterns for this Laravel application.

## Core Testing Philosophy

- **Prioritize feature tests over unit tests** - Feature tests provide the most value by testing the application as a whole
- **Test through HTTP** - Send requests, assert responses, check database state
- **Avoid excessive mocking** - Test with real database interactions
- **Use `RefreshDatabase`** for database isolation
- **Unit tests are the exception** - Only for pure functions without dependencies

## Test Suite Structure example

- Each test file focuses on one user-facing action or endpoint.
- Mirror the application's feature structure, not the code structure
```
tests/
├── Http/              # Controller/HTTP tests
│   ├── Members/
│   │   ├── ShowTest.php
│   │   ├── CreateTest.php
│   │   └── UpdateTest.php
│   ├── Bookings/
│   │   ├── CreateTest.php
│   │   └── FreezeTest.php
│   └── Workouts/
│       └── CreateTest.php
├── Console/           # Artisan command tests
│   └── MarkBookingSlotsCompleteTest.php
└── Unit/              # Pure unit tests (minimal)
    └── UserModelTest.php
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

Configure three test suites in `phpunit.xml`:

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
</testsuites>
```

## Summary

| Principle      | Guideline                                        |
|----------------|--------------------------------------------------|
| Test approach  | Feature tests over unit tests                    |
| Test structure | HTTP request → Assert response → Assert database |
| Organization   | Group by domain, one action per test file        |
| Data setup     | Prefer seeded data, use Pest.php helpers         |
| Database       | Use RefreshDatabase, avoid mocking               |
| Inertia        | Use custom assertion macros                      |
| Refactoring    | Tests verify behavior, not implementation        |
