# Role-Based Access Control Plan

## Context

All management routes (members, trainers, workouts, bookings, settings) currently only require authentication — any logged-in user can reach them. The app needs to restrict access by role, starting with admin-only routes, with trainer and member access levels coming later.

The `role` enum column already exists in the `users` table (`admin`, `trainer`, `member`, default `member`), and `app/Enums/Role.php` already defines those cases. **No migration is needed.**

---

## Middleware vs Policy — Which to Use

| Tool | Purpose | Use here? |
|------|---------|-----------|
| **Middleware** | Route-level gate — "does this user's role allow access to this route group?" | **Yes — now** |
| **Policy** | Model-level gate — "can this user perform this action on this specific record?" | Later, when trainer/member access introduces per-record authorization (e.g., a trainer can only see their own bookings) |

Use **middleware only** for now. Policies are not needed until role permissions diverge at the model level.

---

## Implementation Steps

### 1. Create `EnsureUserHasRole` middleware

**File to create:** `app/Http/Middleware/EnsureUserHasRole.php`

- Accepts one or more role strings as middleware parameters (e.g., `role:admin` or `role:admin,trainer`)
- If the authenticated user's role is not in the allowed list → `abort(403)`
- Reuses the existing `Role` enum from `app/Enums/Role.php`

### 2. Register middleware alias in `bootstrap/app.php`

Inside the `withMiddleware` callback, add:

```php
$middleware->alias(['role' => \App\Http\Middleware\EnsureUserHasRole::class]);
```

### 3. Group admin routes in `routes/web.php`

Wrap all management routes (members, trainers, workouts, bookings, settings) inside a nested `middleware('role:admin')` group, inside the existing auth group.

The dashboard stays accessible to all authenticated users (no role restriction yet).

---

## Future Role Layers

When trainer and member access are added, the same middleware handles it — just change the parameter:

| Route Group | Middleware |
|------------|-----------|
| Admin management | `role:admin` |
| Trainer-facing routes (calendar, booking slots) | `role:admin,trainer` |
| Member-facing routes (own booking history) | `role:admin,trainer,member` or `role:member` |

Policies get introduced alongside trainer/member routes to handle per-record authorization (e.g., a trainer may access the calendar page but only see their own bookings).

---

## Files to Touch

| File | Change |
|------|--------|
| `app/Http/Middleware/EnsureUserHasRole.php` | Create — role gate middleware |
| `bootstrap/app.php` | Register `role` middleware alias |
| `routes/web.php` | Wrap management routes in `middleware('role:admin')` group |

---

## Verification

1. Seed or manually set a user's role to `member` in the database
2. Log in as that user — all management routes should return 403
3. Set the role to `admin` — all management routes should be accessible
4. Run `php artisan test --compact` to ensure existing tests still pass (update any that hit management routes without an admin user)