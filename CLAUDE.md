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

# Queue worker (for processing emails and background jobs)
php artisan queue:work  # Process queued jobs
php artisan queue:work --queue=emails  # Process specific queue
php artisan queue:listen  # Process jobs and watch for new ones

# Scheduler (for scheduled tasks)
php artisan schedule:list  # List all scheduled tasks
php artisan schedule:run   # Run scheduled tasks (use in testing)
php artisan schedule:work  # Run scheduler in foreground (for local dev)
```

## Running Queue and Scheduler Locally

For local development, you need to run both the queue worker (for emails) and scheduler (for hourly tasks) in separate terminal windows.

### Quick Start

**Terminal 1 - Queue Worker:**
```bash
php artisan queue:work
```
Processes queued jobs like welcome emails and owner notifications.

**Terminal 2 - Scheduler:**
```bash
php artisan schedule:work
```
Runs scheduled tasks like the hourly booking slots completion.

**Terminal 3 - Dev Server (Optional):**
```bash
php artisan serve
# or
npm run dev
```

### Testing Without Waiting

**Test Queue Immediately:**
```bash
# Process one job from the queue
php artisan queue:work --once

# View failed jobs
php artisan queue:failed
```

**Test Scheduler Immediately:**
```bash
# Run all due scheduled tasks now
php artisan schedule:run

# Test specific command manually
php artisan lift-station:mark-booking-slots-complete
```

**View What's Scheduled:**
```bash
php artisan schedule:list
```

### Troubleshooting

**Queue not processing:**
- Ensure `QUEUE_CONNECTION=database` in `.env`
- Check jobs table: `SELECT * FROM jobs;`
- Check failed_jobs table for errors

**Scheduler not running:**
- Ensure you're using `schedule:work` (not `schedule:run` which runs once)
- Check timezone matches: `APP_TIMEZONE=Asia/Beirut`
- View scheduled tasks: `php artisan schedule:list`

**Both not working:**
- Restart both terminal processes
- Clear cache: `php artisan cache:clear`
- Check Laravel log: `storage/logs/laravel.log`

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

**API Resources (CRITICAL - Avoid Circular References):**
- NEVER create circular references between resources (e.g., `BookingResource` including `BookingSlotResource::collection()` while `BookingSlotResource` includes `BookingResource`)
- Circular references cause infinite recursion loops that exhaust memory/execution time, resulting in 502 Bad Gateway errors in production
- When relationships are bidirectional, pass related data as separate props instead of nesting resources
- Use `whenLoaded()` to conditionally include relationships, but ensure no circular loading patterns exist

```php
// ❌ AVOID - Circular reference that causes 502 errors
// BookingResource.php
'bookingSlots' => BookingSlotResource::collection($this->whenLoaded('bookingSlots')),

// BookingSlotResource.php
'booking' => new BookingResource($this->whenLoaded('booking')),
// This creates: BookingSlot → Booking → BookingSlots → Booking → ... (infinite loop)

// ✅ GOOD - Pass related collections as separate controller props
// Controller
return Inertia::render('Admin/Bookings/Show', [
    'booking' => BookingResource::make($booking),
    'bookingSlots' => BookingSlotResource::collection($booking->bookingSlots),
]);

// Vue Component
defineProps({
    booking: Object,
    bookingSlots: Array,  // Separate prop, no circular reference
})
```

**Why this matters:**
- Works locally but fails in production due to stricter memory/timeout limits
- PHP-FPM crashes → nginx returns 502 Bad Gateway
- Difficult to debug because error happens at PHP process level, not application level
- Always test resource relationships by loading them from both directions

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

**Template Structure & Component Encapsulation:**
- Keep templates simple and flat - extract complex sections into dedicated components
- Each component should own its presentation logic (UI, dropdowns, icons, etc.)
- Parent components should only handle data flow and business logic
- Avoid deeply nested markup - extract into Partials when sections become complex
- Component composition > inline markup

```vue
<!-- ✅ Good - Clean, flat template with encapsulated components -->
<template>
    <PageHeader>
        <div class="flex w-full justify-between items-center gap-2 font-normal">
            <members-search
                :search="searchQuery"
                @update:search="handleSearchChange"
            />
            <members-filters
                :active-training="activeTraining"
                @update:active-training="handleFilterChange"
            />
            <Link :href="route('admin.members.create')">
                <primary-button>
                    <PlusIcon class="size-5" />
                </primary-button>
            </Link>
        </div>
    </PageHeader>
</template>

<!-- ❌ Avoid - Nested markup with mixed concerns -->
<template>
    <PageHeader>
        <div class="flex w-full justify-between items-center gap-2 font-normal">
            <div class="flex-1 max-w-xs">
                <text-input
                    v-model="searchQuery"
                    @input="performSearch"
                    placeholder="Search members..."
                />
            </div>
            <dropdown direction="left">
                <template #trigger>
                    <button class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer">
                        <FunnelIcon class="w-5 h-5 text-zinc-500" />
                    </button>
                </template>
                <div class="space-y-3">
                    <div class="text-xs font-semibold text-zinc-700 uppercase">Filters</div>
                    <div class="flex items-center gap-2">
                        <switch-input v-model="activeTraining" @change="performSearch" />
                        <label class="text-sm text-[#71717b]">Currently training</label>
                    </div>
                </div>
            </dropdown>
            <Link :href="route('admin.members.create')">
                <primary-button><PlusIcon /></primary-button>
            </Link>
        </div>
    </PageHeader>
</template>
```

**Component Extraction Guidelines:**

✅ **DO:**
- Extract logical sections into Partials (e.g., `MembersSearch`, `MembersFilters`)
- Let components own their UI patterns (dropdowns, modals, icons inside the component)
- Keep parent templates readable at a glance (3-5 direct child components max)
- Use props for data down, events for actions up
- Name components by their domain purpose, not their UI element (`MembersFilters` not `FilterDropdown`)

❌ **DON'T:**
- Leave complex markup inline when it can be extracted
- Split dropdown trigger and content between parent and child
- Create separate mobile/desktop templates when filters can be hidden in dropdowns
- Expose internal UI implementation details (icons, buttons) to parent components
- Create generic "wrapper" components - make them domain-specific

**Example Component Extraction:**

```vue
<!-- MembersFilters.vue - Owns all filter UI -->
<template>
    <dropdown direction="left">
        <template #trigger>
            <button class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer">
                <FunnelIcon class="w-5 h-5 text-zinc-500" />
            </button>
        </template>
        <div class="space-y-3">
            <div class="text-xs font-semibold text-zinc-700 uppercase">Filters</div>
            <div class="flex items-center gap-2">
                <switch-input :model-value="activeTraining" @update:model-value="handleChange" />
                <label class="text-sm text-[#71717b]">Currently training</label>
            </div>
        </div>
    </dropdown>
</template>

<script setup>
import { FunnelIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Layout/Dropdown.vue'
import SwitchInput from '@/Components/Form/SwitchInput.vue'

defineProps({
    activeTraining: { type: Boolean, required: true },
})

const emit = defineEmits(['update:activeTraining'])

const handleChange = (value) => {
    emit('update:activeTraining', value)
}
</script>
```

**Search & Filtering:**
- Implement debounced search with `setTimeout`
- Use Inertia router for URL state management
- Preserve query strings with `withQueryString()`
- Handle filter state with reactive refs

**Route Matching & Active States:**
- ALWAYS use Ziggy's `route().current()` for checking active routes, NEVER compare URLs directly
- Use wildcard patterns (`.*`) for matching route groups
- Store both `href` (for navigation) and `routeName` (for active state checking) in nav items
- This ensures consistent behavior regardless of URL structure changes

```vue
<!-- ✅ Good - Using Ziggy route matching -->
<script setup>
const navItems = [
    {
        name: 'Members',
        href: route('admin.members.index'),        // For navigation
        routeName: 'admin.members.*',              // For active state matching
        icon: UsersIcon,
    },
]

const isActive = (routeName) => {
    return route().current(routeName)  // Uses Ziggy's route matching
}
</script>

<template>
    <Link
        :href="item.href"
        :class="isActive(item.routeName) ? 'active' : 'inactive'"
    >
        {{ item.name }}
    </Link>
</template>

<!-- ❌ Avoid - URL string comparison -->
<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const isActive = (href) => {
    // Fragile - breaks if URL structure changes
    return page.url.startsWith(href)
}
</script>

<!-- ❌ Avoid - Hardcoded URL paths -->
<script setup>
const isActive = () => {
    return window.location.pathname.includes('/members')  // Very fragile
}
</script>
```

**Route Matching Best Practices:**
- Use exact route names for single pages: `'dashboard'`
- Use wildcards for route groups: `'admin.members.*'` matches all member-related routes
- Combine with Inertia's Link component for proper SPA navigation
- This approach is maintainable and survives route refactoring

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

**Inertia Testing Patterns:**
- ALWAYS use Inertia's `assertInertia()` assertions for testing Inertia responses
- NEVER manually access `viewData()` to extract props
- Use custom test macros (`assertHasComponent`, `assertHasProp`, `assertHasResource`, `assertHasPaginatedResource`) for common assertions
- Use `has()` to check existence and count of collections
- Use `where()` to assert specific property values
- Chain assertions for cleaner, more readable tests

```php
// ✅ Good - Use Inertia assertions
use Inertia\Testing\AssertableInertia;

test('it shows expiring bookings', function () {
    $expiringBooking = createSoonToExpireBooking($member, $trainer);

    actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('bookings.expiring', 1)
            ->where('bookings.expiring.0.id', $expiringBooking->id)
        );
});

// ✅ Good - Asserting empty collections
actingAsAdmin()
    ->get(route('dashboard'))
    ->assertStatus(200)
    ->assertInertia(fn (AssertableInertia $page) => $page
        ->has('bookings.expiring', 0)
    );

// ❌ Avoid - Manual viewData access
$response = actingAsAdmin()->get(route('dashboard'));
$expiringBookings = $response->viewData('page')['props']['bookings']['expiring'];
expect($expiringBookings)->toBeArray()
    ->and($expiringBookings)->toHaveCount(1)
    ->and($expiringBookings[0]['id'])->toBe($expiringBooking->id);

// ✅ Good - Using custom test macros (defined in TestingServiceProvider)

// assertHasComponent - Check the Inertia component being rendered
actingAsAdmin()
    ->get(route('admin.members.index'))
    ->assertHasComponent('Admin/Members/Index')
    ->assertStatus(200);

// assertHasProp - Check a specific prop value
actingAsAdmin()
    ->get(route('admin.members.index', ['search' => 'John']))
    ->assertHasComponent('Admin/Members/Index')
    ->assertHasProp('search', 'John')
    ->assertStatus(200);

// assertHasResource - Check a JsonResource prop
actingAsAdmin()
    ->get(route('admin.members.show', $member))
    ->assertHasComponent('Admin/Members/Show')
    ->assertHasResource('member', MemberResource::make($member))
    ->assertStatus(200);

// assertHasPaginatedResource - Check a paginated ResourceCollection
actingAsAdmin()
    ->get(route('admin.members.index'))
    ->assertHasComponent('Admin/Members/Index')
    ->assertHasPaginatedResource('members', MemberResource::collection($members))
    ->assertStatus(200);

// ✅ Good - Combining macros with nested property assertions
actingAsAdmin()
    ->get(route('admin.bookings-slots.show', $bookingSlot))
    ->assertHasComponent('Admin/BookingsSlots/Show')
    ->assertHasResource('bookingSlot', BookingSlotResource::make($bookingSlot))
    ->assertHasProp('bookingSlot.workouts.0.name', $workout->name)
    ->assertHasProp('bookingSlot.workouts.0.category', $workout->category->value)
    ->assertStatus(200);
```

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

function createSoonToExpireBooking(User $member, User $trainer): Booking
{
    // Specific scenario: booking with 2 remaining sessions
}

test('it handles expiring bookings', function () {
    setupUsersAndBookings(); // Baseline
    $booking = createSoonToExpireBooking($member, $trainer); // Test-specific
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
- When adding new model field, update related seeder, factory, test setup db if applicable
- when writing tests, convert multiple expectations into chain

## Email System

**Email Configuration:**
- Mail driver: SMTP (Mailtrap for development/testing)
- Queue connection: database
- All emails are queued to avoid blocking HTTP requests
- Owner notification emails sent to addresses in `OWNERS_EMAILS` env variable

**Email Types:**
1. **Member Welcome Email** (`App\Mail\Member\WelcomeEmail`)
   - Sent to new members upon registration
   - Contains personalized greeting and motivational message

2. **Booking Slot Reminder Email** (`App\Mail\Member\BookingSlotReminderEmail`)
   - Sent to members the night before their training session (9pm daily)
   - Contains session date, time, trainer name, and a random motivational message
   - 15 different motivational messages to keep emails fresh and engaging

3. **Owner Notification Email** (`App\Mail\Owner\NewMemberEmail`)
   - Sent to gym owner(s) when a new member registers
   - Contains member details and link to profile

**Testing Emails:**
```bash
# View queued jobs
php artisan queue:work --once  # Process one job

# Check Mailtrap inbox
# Visit https://mailtrap.io to view sent test emails

# Run email tests
php artisan test --filter=MemberWelcomeEmailTest  # Test welcome emails
php artisan test --filter=BookingSlotReminderTest  # Test reminder emails
php artisan test --filter=OwnerNewMemberEmailTest  # Test owner notification emails
php artisan test --filter="MemberWelcomeEmailTest|BookingSlotReminderTest|OwnerNewMemberEmailTest"  # Run all email tests
```

**Email Previews:**
Visit these URLs in development to preview emails:
- All member emails: `/preview-emails/member`
- Welcome email: `/preview-emails/member/welcome`
- Session reminder: `/preview-emails/member/booking-slot-reminder`
- Owner notification: `/preview-emails/owner`

**Email Branding:**
- Font: Inter (via Google Fonts)
- Logo: `public/logo.png`
- Primary button: gray-800 background, white text
- Layout: `resources/views/emails/layouts/branded.blade.php`
- Theme: `resources/views/vendor/mail/html/themes/liftstation.css`

## Scheduled Tasks

**Timezone:** Asia/Beirut (UTC+2 in winter, UTC+3 in summer with DST)

**Scheduled Commands:**
1. **Mark Booking Slots Complete** (`lift-station:mark-booking-slots-complete`)
   - Runs: Every hour (at :00 - e.g., 1:00, 2:00, 3:00 AM/PM Beirut time)
   - Purpose: Updates past booking slots to "Complete" status
   - Preserves: Cancelled and Frozen slots (not changed)
   - Logic: Where `end_time < now()` and status is Upcoming
   - Logs: Each booking slot marked complete with member name and date/time

2. **Send Booking Slot Reminders** (`lift-station:send-booking-slot-reminders`)
   - Runs: Daily at 9:00 PM Beirut time
   - Purpose: Sends reminder emails to members for their training sessions tomorrow
   - Only sends for: Upcoming booking slots (not cancelled/frozen/complete)
   - Logs: Each reminder sent with member name and session date/time

**Local Development:**
```bash
# View all scheduled tasks
php artisan schedule:list

# Run scheduler manually (executes due tasks)
php artisan schedule:run

# Run scheduler in foreground (keeps running and executes at proper times)
php artisan schedule:work

# Test specific commands manually
php artisan lift-station:mark-booking-slots-complete
php artisan lift-station:send-booking-slot-reminders
```

**Production Setup:**
Add this cron entry to run the Laravel scheduler every minute:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Laravel's scheduler will then determine which commands should run based on their schedule.

**Testing:**
```bash
php artisan test --filter=CommandMarkBookingSlotsCompleteTest
```
- when feature/task is not working over 3 iterations, ask for debugging info (console log, dd(), etc...) so i can help you