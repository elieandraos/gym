# Dashboard Homepage Redesign

## Context
Remove Chart.js charts (TrainersBarChart, GenderPieChart) from the dashboard and replace the left column with a "Today at the gym" macro stat strip. The expiring/unpaid/frozen actionable lists are kept. Charts move to a future Insights page.

## Proposed Layout

```
[Header: "Ready to crush today?"]

[4 stat cards — 2×2 on mobile, 4-in-a-row on md+]
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ Active       │ Sessions     │ Trainers     │ New Members  │
│ Members      │ Today        │ On Duty      │ This Month   │
│   42  ↑3     │    18        │    5         │    7         │
└──────────────┴──────────────┴──────────────┴──────────────┘

[2-column grid — existing, unchanged]
┌─────────────────────┬────────────────────┐
│   Expiring Soon     │  Unpaid / Frozen   │
└─────────────────────┴────────────────────┘
```

## Changes

### 1. `app/Http/Controllers/DashboardController.php`
- **Remove**: `trainers` query (withCount trainerActiveBookings), `maleCount`, `femaleCount` queries
- **Add to `stats`**:
  - `sessions_today` — count of `booking_slots` where `DATE(start_time) = today` and `status != 'cancelled'`
  - `trainers_today` — count of distinct `trainer_id` from bookings that have sessions today
  - `new_enrollments_month` — count of `bookings` where `created_at >= start of current month`
- **Remove from return**: `trainers` prop, `male_members`, `female_members`

### 2. `resources/js/Pages/Admin/Dashboard/Index.vue`
- **Remove**: imports and usage of `TrainersBarChart`, `GenderPieChart`
- **Remove**: `trainers` defineProps entry
- **Replace left column** (StatsCard + 2 charts) with a **full-width 4-card stat grid**:
  - Use existing `StatsCard` component × 4 (it already supports optional `change` prop)
  - Grid: `grid grid-cols-2 md:grid-cols-4 gap-6` above the 2-col list layout
- **Keep unchanged**: 2-column grid with `ExpiringSoonCard` + `UnpaidCard`

### 3. Chart files — **keep, do not delete**
- `TrainersBarChart.vue` and `GenderPieChart.vue` will be reused on the future Insights page

## New Stat Definitions

| Stat | Label | Source |
|---|---|---|
| `active_members` | Active Members (avg. age X) | existing query |
| `sessions_today` | Sessions Today | `booking_slots` where DATE(start_time) = today, status ≠ cancelled |
| `trainers_today` | Trainers On Duty | distinct `trainer_id` from above |
| `new_enrollments_month` | New Members This Month | `bookings.created_at >= first of month` |

## Files to Modify
- `app/Http/Controllers/DashboardController.php`
- `resources/js/Pages/Admin/Dashboard/Index.vue`

## Files to Keep (no changes)
- `resources/js/Pages/Admin/Dashboard/Partials/TrainersBarChart.vue`
- `resources/js/Pages/Admin/Dashboard/Partials/GenderPieChart.vue`
- `resources/js/Pages/Admin/Dashboard/Partials/StatsCard.vue` (reused ×4)
- `resources/js/Pages/Admin/Dashboard/Partials/ExpiringSoonCard.vue`
- `resources/js/Pages/Admin/Dashboard/Partials/UnpaidCard.vue`

## Tests
- Update or add feature test for `DashboardController` to assert new stat keys (`sessions_today`, `trainers_today`, `new_enrollments_month`) are present and `trainers` prop is gone
- Run: `php artisan test --compact --filter=DashboardController`