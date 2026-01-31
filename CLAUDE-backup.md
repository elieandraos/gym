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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.8
- inertiajs/inertia-laravel (INERTIA) - v2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/nightwatch (NIGHTWATCH) - v1
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- tightenco/ziggy (ZIGGY) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA) - v2
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `pest-testing` — Tests applications using the Pest 4 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, browser testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.
- `inertia-vue-development` — Develops Inertia.js v2 Vue client-side applications. Activates when creating Vue pages, forms, or navigation; using &lt;Link&gt;, &lt;Form&gt;, useForm, or router; working with deferred props, prefetching, or polling; or when user mentions Vue with Inertia, Vue pages, Vue forms, or Vue navigation.
- `tailwindcss-development` — Styles applications using Tailwind CSS v4 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== inertia-laravel/v2 rules ===

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scrolling (merging props + `WhenVisible`), lazy loading on scroll, polling, prefetching.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` every time you're working with a Pest or testing-related task.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.
</laravel-boost-guidelines>
