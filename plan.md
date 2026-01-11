# Fix 502 Bad Gateway: Remove Circular Reference in BookingResource

## Problem Description

Production URL `https://lift-station.fitness/bookings-slots/1215/show` returns **502 Bad Gateway** nginx error.

### Root Cause: Infinite Recursion Loop

There's a circular reference between `BookingSlotResource` and `BookingResource`:

1. **BookingSlotResource.php:32** loads `BookingResource`:
   ```php
   'booking' => new BookingResource($this->whenLoaded('booking')),
   ```

2. **BookingResource.php:47** loads `BookingSlotResource::collection`:
   ```php
   'bookingSlots' => $slots->isNotEmpty() ? BookingSlotResource::collection($slots) : null,
   ```

This creates infinite loop:
```
BookingSlot → Booking → BookingSlots → Booking → BookingSlots → ...
```

### Why It Works Locally But Not Production
- Local has higher `memory_limit` and `max_execution_time` in php.ini
- Production has stricter PHP-FPM settings (lower memory/timeout limits)
- Production may have more booking slot data, making recursion deeper
- PHP runs out of memory or hits max execution time → PHP-FPM crashes → nginx returns 502

---

## Impact Analysis: What Will Break

### ❌ WILL BREAK (1 Critical Place)

#### Booking Detail Page - `/admin/bookings/{id}`
**Files:**
- Controller: `app/Http/Controllers/Admin/BookingsController.php:79`
- Page: `resources/js/Pages/Admin/Bookings/Show.vue:28-29`
- Component: `resources/js/Pages/Admin/Bookings/Partials/BookingSessions.vue:62`

**Current Code:**
```javascript
// Show.vue - destructures bookingSlots from props.booking
const {
    member, trainer, bookingSlots, status, formatted_start_date, formatted_end_date
} = props.booking
```

**Impact:** The entire booking sessions table won't render. Users can't see the list of all sessions (upcoming, completed, cancelled, frozen) for a booking.

---

### ✅ WILL NOT BREAK (Safe Areas)

**Dashboard Cards:**
- `ExpiringSoonCard.vue` - Only uses: `id`, `member.name`, `nb_remaining_sessions`, upcoming session data
- `UnpaidCard.vue` - Only uses: `id`, `member.name`, `trainer.name`, `title`, `is_paid`

**Freeze/Unfreeze Pages:**
- `FreezeBooking/Index.vue` - Only uses: `id`, `member` (for header)
- `UnfreezeBooking/Index.vue` - Gets `frozenSlots` as separate prop, not from `booking.bookingSlots`

**Booking Create/Renew Form:**
- `Bookings/Create.vue` - Only uses: `member.id`, `trainer.id`, `nb_sessions`, `is_paid`, `schedule_days`, `end_date`

**Emails:**
- No email templates use `BookingResource` directly

---

## Solution

Remove the circular reference by removing `bookingSlots` from `BookingResource` and loading booking slots separately in the booking detail page.

---

## Implementation Plan

### Step 1: Remove Circular Reference in BookingResource
**File:** `app/Http/Resources/BookingResource.php`

**Action:** Remove line 47:
```php
// REMOVE THIS LINE:
'bookingSlots' => $slots->isNotEmpty() ? BookingSlotResource::collection($slots) : null,
```

**Note:** Keep lines 16-21 that use `$slots` for calculations. They still work because they use `relationLoaded('bookingSlots')` internally and calculate:
- `upcoming_session_*` fields (lines 48-50)
- `nb_completed_sessions` and `nb_remaining_sessions` (lines 51-52)

---

### Step 2: Fix Booking Detail Page (Show.vue)

**Controller:** `app/Http/Controllers/Admin/BookingsController.php:79`

**Change from:**
```php
public function show(Booking $booking): Response
{
    $booking->load([
        'member.memberActiveBooking',
        'trainer',
        'bookingSlots',
    ]);

    return Inertia::render('Admin/Bookings/Show', [
        'booking' => BookingResource::make($booking),
    ]);
}
```

**Change to:**
```php
public function show(Booking $booking): Response
{
    $booking->load([
        'member.memberActiveBooking',
        'trainer',
        'bookingSlots',
    ]);

    return Inertia::render('Admin/Bookings/Show', [
        'booking' => BookingResource::make($booking),
        'bookingSlots' => BookingSlotResource::collection(
            $booking->bookingSlots->sortBy('start_time')->values()
        ),
    ]);
}
```

---

### Step 3: Update Vue Component

**Vue Component:** `resources/js/Pages/Admin/Bookings/Show.vue`

**Change from:**
```vue
<script setup>
const props = defineProps({
    booking: Object,
})

const {
    member, trainer, bookingSlots, status, formatted_start_date, formatted_end_date
} = props.booking
</script>

<template>
    <BookingSessions :booking-slots="bookingSlots" :trainer="trainer"></BookingSessions>
</template>
```

**Change to:**
```vue
<script setup>
const props = defineProps({
    booking: Object,
    bookingSlots: Array,  // NEW: separate prop
})

const {
    member, trainer, status, formatted_start_date, formatted_end_date
} = props.booking
</script>

<template>
    <BookingSessions :booking-slots="props.bookingSlots" :trainer="trainer"></BookingSessions>
</template>
```

---

### Step 4: Make BookingSessions Component Defensive

**Component:** `resources/js/Pages/Admin/Bookings/Partials/BookingSessions.vue`

**Change from:**
```vue
<script setup>
const props = defineProps({
    bookingSlots: { type: Array, required: true },
    trainer: Object,
})
</script>
```

**Change to:**
```vue
<script setup>
const props = defineProps({
    bookingSlots: { type: Array, default: () => [] },  // Make optional with default
    trainer: Object,
})
</script>
```

---

### Step 5: Remove Last Session Recap from Member Show Page

**Vue Component:** `resources/js/Pages/Admin/Members/Show.vue`

**Remove import (line 70):**
```vue
import LastSessionRecap from '@/Pages/Admin/Members/Partials/LastSessionRecap.vue'
```

**Remove table row (lines 24-29):**
```vue
<tr class="border-b border-zinc-100">
    <td class="text-[#71717b] py-4">Last workouts</td>
    <td class="py-4">
        <LastSessionRecap :active-booking="activeBooking" />
    </td>
</tr>
```

---

### Step 6: Update Tests

**File:** `tests/Feature/Admin/BookingsTest.php:58`

**Current test:**
```php
actingAsAdmin()
    ->get(route('admin.bookings.show', $booking))
    ->assertHasComponent('Admin/Bookings/Show')
    ->assertHasResource('booking', BookingResource::make($booking))
    ->assertStatus(200);
```

**Update to:**
```php
actingAsAdmin()
    ->get(route('admin.bookings.show', $booking))
    ->assertHasComponent('Admin/Bookings/Show')
    ->assertHasResource('booking', BookingResource::make($booking))
    ->assertInertia(fn (AssertableInertia $page) => $page
        ->has('bookingSlots')
    )
    ->assertStatus(200);
```

---

## Files to Modify

### Backend (PHP)
1. `app/Http/Resources/BookingResource.php` - Remove line 47
2. `app/Http/Controllers/Admin/BookingsController.php` - Add separate `bookingSlots` prop in `show()` method

### Frontend (Vue)
3. `resources/js/Pages/Admin/Bookings/Show.vue` - Accept `bookingSlots` as separate prop
4. `resources/js/Pages/Admin/Bookings/Partials/BookingSessions.vue` - Make `bookingSlots` prop optional with default
5. `resources/js/Pages/Admin/Members/Show.vue` - Remove LastSessionRecap component import and table row

### Tests
6. `tests/Feature/Admin/BookingsTest.php` - Update assertions for separate `bookingSlots` prop

---

## Testing Checklist

After implementing the fix:

- [ ] **Booking Detail Page** (`/admin/bookings/{id}`):
  - [ ] Sessions table displays correctly
  - [ ] All session statuses visible (upcoming, completed, cancelled, frozen)
  - [ ] Can click individual sessions to view details

- [ ] **Dashboard** (`/`):
  - [ ] "Expiring Soon" card displays correctly
  - [ ] "Unpaid Bookings" card displays correctly
  - [ ] No errors in browser console

- [ ] **Production URL**:
  - [ ] `https://lift-station.fitness/bookings-slots/1215/show` no longer returns 502
  - [ ] Booking slot detail page loads successfully
  - [ ] No PHP memory errors in logs

- [ ] **Run Tests**:
  - [ ] `php artisan test --filter=BookingsTest`
  - [ ] All tests pass

---

## Notes

- The circular reference existed before but only triggered when loading booking relationship on booking slot show page
- Booking slot ID 1215 has no circuits yet (confirmed by user)
- After fix, verify no other places depend on `booking.bookingSlots` nested structure