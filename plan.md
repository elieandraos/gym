# Inertia v3 Best Practices Audit

## Summary

The codebase uses Inertia v3 but only the basics (`Inertia::render()`, `useForm`, `router`). Below are 6 concrete improvements using v3-specific features — ordered by impact.

---

## 1. Remove Axios (Dead Code)

**Why:** Axios is installed but never used in any Vue component. Only `bootstrap.js` sets it up as legacy boilerplate.

**Files:**
- `package.json` — remove `axios`
- `resources/js/bootstrap.js` — remove the axios import + `window.axios` setup

---

## 2. Dashboard — Deferred Props

**Why:** `DashboardController` runs 5+ heavy queries before the page renders, blocking initial load. The Vue template is **already ready** for this — it has null guards (`stats?.active_members || 0`) and `:loading="!stats"` on every card. Skeleton states are already wired up.

**Backend:** `app/Http/Controllers/DashboardController.php`
```php
return Inertia::render('Admin/Dashboard/Index', [
    'stats'    => Inertia::defer(fn () => [...]),
    'bookings' => Inertia::defer(fn () => [...]),
    'trainers' => Inertia::defer(fn () => [...]),
]);
```

**Frontend:** `resources/js/Pages/Admin/Dashboard/Index.vue`
Wrap deferred sections with `<Deferred>` and `<template #fallback>` for correctness. The existing `:loading` props on child cards already handle the visual state.

---

## 3. Dashboard `markAsPaid` — Optimistic Update

**Why:** Currently splices the booking from the list in `onSuccess` (after server responds). With optimistic updates, the removal is instant with automatic rollback on failure.

**File:** `resources/js/Pages/Admin/Dashboard/Index.vue` (lines 140–151)

```javascript
const markAsPaid = (bookingId) => {
    router.patch(route('admin.bookings.mark-as-paid', bookingId), {}, {
        preserveScroll: true,
        optimistic: (page) => ({
            ...page,
            props: {
                ...page.props,
                bookings: {
                    ...page.props.bookings,
                    unpaid: page.props.bookings.unpaid.filter(b => b.id !== bookingId),
                },
            },
        }),
    })
}
```
Drop the `onSuccess` block entirely.

---

## 4. Sidebar Navigation — Prefetch + Instant Visits

**Why:** Every sidebar link is a candidate for prefetching (hover triggers data preload). The dashboard logo link is a perfect `instant` candidate.

**Files:**
- `resources/js/Components/NavLink.vue` — add a `prefetch` prop forwarded to `<Link>`
- `resources/js/Layouts/Sidebar.vue`:
  - Logo `<Link>` (line 6): add `instant` — dashboard is the most-visited page
  - Nav items: pass `prefetch` via `NavLink` prop

---

## 5. Error Pages

**Why:** No Inertia-aware error handling. On 404/500 during SPA navigation, users get a broken response instead of a proper in-app error page.

**a) `bootstrap/app.php`** — add renderer in `withExceptions`:
```php
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (Throwable $e, Request $request) {
        if ($request->inertia()) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            return Inertia::render('Error/Index', ['status' => $status])
                ->toResponse($request)
                ->setStatusCode($status);
        }
    });
})
```

**b) Create `resources/js/Pages/Error/Index.vue`**
- Display the status code + human-readable message
- Link back to dashboard via `<Link>`
- Reuse `AppLayout` or a minimal layout

---

## 6. Network Error Handling

**Why:** No fallback if the connection drops during a request. v3 renames the event from `exception` → `networkError`.

**File:** `resources/js/app.js`
```javascript
import { router } from '@inertiajs/vue3'

router.on('networkError', () => {
    // Set a reactive flag → render a fixed banner in AppLayout
})
```
Add a global reactive ref + a fixed banner in `AppLayout.vue` when the flag is true.

---

## 7. Members / Trainers / Workouts Lists — Conditional Defer + Skeleton

> **Why not infinite scroll?** `Inertia::merge()` + infinite scroll works well for content feeds and activity logs. For these admin list pages, regular pagination is the right choice: admins search by name (debounced search already solves discovery), the dataset is bounded (~100–500 members), and pagination preserves the user's position in the list across refreshes and form submissions. Infinite scroll would add complexity without UX benefit here.

**Why defer:** The list pages run paginated queries before render. Deferring them gives an instant initial page paint with a skeleton table, then the data appears. The trick: search/filter requests (`?search=`, `?trainingStatus=`) must NOT defer — otherwise every keystroke triggers the skeleton flash. We conditionally defer only on the clean initial load.

**Additional bug fix:** Both `Members/Index.vue` (line 51) and `Trainers/Index.vue` (line 42) **destructure props statically** at setup time:
```javascript
const { data, meta } = props.members  // ← not reactive, breaks with defer
```
When `members` starts as `null` (deferred), `data` and `meta` are `undefined` forever — they don't update when the deferred response arrives. This needs to be converted to computed properties.

### Backend changes (same pattern for Members, Trainers, Workouts)

**`app/Http/Controllers/Admin/MembersController.php`** — `index()`:
```php
// Only defer on the clean initial load (no active search/filter)
$membersProp = request()->hasAny(['search', 'trainingStatus'])
    ? MemberResource::collection($members)
    : Inertia::defer(fn () => MemberResource::collection($members));

return Inertia::render('Admin/Members/Index', [
    'members' => $membersProp,
    'search' => request('search'),
    'trainingStatus' => $trainingStatus,
]);
```

Same pattern for `TrainersController::index()` (guard on `search`) and `WorkoutController::index()` (guard on `search` + `selectedCategories`).

### Frontend changes

**`resources/js/Pages/Admin/Members/Index.vue`:**
1. Import `Deferred` from `@inertiajs/vue3`
2. Replace static destructuring with computed:
```javascript
import { computed, ref, watch } from 'vue'
import { Deferred, Link, router } from '@inertiajs/vue3'

const data = computed(() => props.members?.data ?? [])
const meta = computed(() => props.members?.meta)
```
3. Wrap `<MembersList>` in `<Deferred data="members">` with a skeleton fallback:
```vue
<Deferred data="members">
    <template #fallback>
        <!-- pulsing skeleton rows matching the table structure -->
        <div class="animate-pulse space-y-3 p-4">
            <div v-for="i in 5" :key="i" class="h-14 bg-zinc-100 rounded-lg" />
        </div>
    </template>
    <MembersList :data="data" :headers="headers" :links="meta?.links ?? []" />
</Deferred>
```

**`resources/js/Pages/Admin/Trainers/Index.vue`:** Same fix — computed `data`/`meta`, `<Deferred data="trainers">` wrapper, skeleton fallback. Note: current code has no null guard on `props.trainers` (line 42) which would crash if the prop starts null — this is a latent bug fixed by the computed approach.

**Same for Workouts/Index.vue** once confirmed it follows the same pattern.

**How `<Deferred>` handles search:** When the user searches, `members` is returned non-deferred in the Inertia response. `<Deferred>` detects this and renders the list immediately — no skeleton flash. Only the initial clean load shows the skeleton.

---

## Files Changed

| File | Change |
|---|---|
| `package.json` | Remove `axios` |
| `resources/js/bootstrap.js` | Remove axios setup |
| `app/Http/Controllers/DashboardController.php` | Wrap all 3 props with `Inertia::defer()` |
| `resources/js/Pages/Admin/Dashboard/Index.vue` | Add `<Deferred>` wrappers; replace `onSuccess` splice with `optimistic` |
| `resources/js/Components/NavLink.vue` | Add `prefetch` prop |
| `resources/js/Layouts/Sidebar.vue` | Add `instant` to logo link; `prefetch` to nav items |
| `bootstrap/app.php` | Add Inertia error renderer |
| `resources/js/Pages/Error/Index.vue` | **New** error page component |
| `resources/js/app.js` | Add `networkError` handler |
| `app/Http/Controllers/Admin/MembersController.php` | Conditional defer on `members` prop |
| `app/Http/Controllers/Admin/TrainersController.php` | Conditional defer on `trainers` prop |
| `app/Http/Controllers/Admin/WorkoutController.php` | Conditional defer on `workouts` prop |
| `resources/js/Pages/Admin/Members/Index.vue` | Computed props; `<Deferred>` + skeleton; fix latent bug |
| `resources/js/Pages/Admin/Trainers/Index.vue` | Computed props; `<Deferred>` + skeleton; fix latent bug |
| `resources/js/Pages/Admin/Workouts/Index.vue` | Computed props; `<Deferred>` + skeleton |