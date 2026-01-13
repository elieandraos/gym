# Production 502 Error Debugging Plan

## Status Update

### ✅ Refactoring COMPLETED (2 commits)
1. **Commit c82661b**: Updated CLAUDE.md with new architectural pattern
2. **Commit e4734bc**: Removed circular reference - all code refactored
   - Removed `booking` from `BookingSlotResource`
   - Updated 3 controllers to pass `booking` as separate prop
   - Removed all 3 `unsetRelation()` workarounds
   - Updated 4 Vue components to accept `booking` as direct prop
   - **All tests passing locally** ✅

### ❌ Current Issue: Production Still Shows 502 Error

**Environment:**
- Deployed via **Laravel Forge** (handles cache/restarts automatically)
- **PHP Version**: 8.4.14
- **Log Location**: `/var/log/php8.4-fpm.log`
- **Laravel Log**: Empty (no errors) → error happening at PHP-FPM level

---

## Debugging Steps (Run on Production Server)

### Step 1: Check PHP-FPM Error Log 🔥 MOST IMPORTANT

```bash
# SSH into production server
ssh forge@Liftstation

# Check PHP-FPM log for actual error
sudo tail -200 /var/log/php8.4-fpm.log

# Check nginx error log
sudo tail -200 /var/log/nginx/error.log

# Or check in real-time (run this, then trigger the 502 in browser)
sudo tail -f /var/log/php8.4-fpm.log
```

**What to look for:**
- "Maximum execution time exceeded"
- "Allowed memory size exhausted"
- "Undefined property" or "Undefined array key"
- "Segmentation fault"
- Stack traces mentioning any Resource classes

---

### Step 2: Identify Which Endpoint is Failing

```bash
# Test endpoints to find which one returns 502
curl -I https://lift-station.fitness/dashboard
curl -I https://lift-station.fitness/bookings
curl -I https://lift-station.fitness/bookings/1
curl -I https://lift-station.fitness/bookings-slots/1215
```

Note which URL returns `HTTP/2 502`

---

### Step 3: Verify Code Deployment

```bash
cd /home/forge/lift-station.fitness

# Check if latest commits are deployed
git log -2 --oneline
# Should show:
# e4734bc :recycle: Refactor circular reference handling in API resources
# c82661b :memo: Update CLAUDE.md with better circular reference pattern

# Verify BookingSlotResource was updated (should return NOTHING)
grep -n "booking.*BookingResource" app/Http/Resources/BookingSlotResource.php

# Verify controllers were updated (should show booking as separate prop)
grep -n "BookingResource::make.*booking" app/Http/Controllers/Admin/BookingSlotsController.php
```

---

### Step 4: Check Frontend Build Status

```bash
# Check if frontend assets were built after deployment
ls -lh public/build/manifest.json
# Check timestamp - should be recent (within last few hours)

# Verify manifest exists and is not empty
cat public/build/manifest.json | head -30
```

---

### Step 5: Check Forge Deployment Log

**Via Forge Dashboard:**
1. Go to https://forge.laravel.com
2. Select your site
3. Click "Deployments" tab
4. View latest deployment log
5. Verify these steps ran successfully:
   - `git pull` succeeded
   - `composer install` succeeded
   - **`npm ci` ran**
   - **`npm run build` or `npm run production` ran**
   - PHP-FPM restarted

---

### Step 6: Search for Other Circular References

```bash
cd /home/forge/lift-station.fitness

# Check if any other resources might have circular references
grep -r "BookingSlotResource" app/Http/Resources/

# Check MemberResource (it has bookings relationship)
grep -A 10 "active_booking\|scheduled_bookings\|completed_bookings" app/Http/Resources/MemberResource.php

# Check if there are any other resource pairs that might circular reference
find app/Http/Resources -name "*.php" -exec basename {} \; | sort
```

---

## Most Likely Root Causes

### 🎯 Cause 1: Frontend Not Rebuilt (MOST LIKELY)

**Symptom:** Vue components still have old code expecting `bookingSlot.booking` nested property

**Check:**
```bash
# Verify BookingSlotHeader has booking as direct prop
grep -A 5 "defineProps" resources/js/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue
# Should show: booking: { type: Object, required: true },
```

**Solution:**
1. Ensure deployment script has npm build:
   ```bash
   npm ci
   npm run build
   ```
2. Trigger new deployment via Forge dashboard
3. Clear browser cache and test

---

### 🎯 Cause 2: Another Circular Reference Exists

**Symptom:** Different resource pair causing same infinite loop

**Check:**
```bash
# Check MemberResource for circular references
cat app/Http/Resources/MemberResource.php | grep -A 3 "bookings"

# Check if any controller loads too many nested relationships
grep -r "load\(\[" app/Http/Controllers/Admin/ | grep -i booking
```

**Potential culprits:**
- `MemberResource` → includes active/scheduled/completed bookings
- Those bookings might auto-load `bookingSlots` (via eager loading in model)
- Those slots might try to load `booking` → circular reference

---

### 🎯 Cause 3: Old Session Data Cached

**Symptom:** Inertia has old cached page data structure

**Solution:**
```bash
# Clear session cache
php artisan session:clear

# Or manually via Forge: Site > Commands > "session:clear"
```

---

### 🎯 Cause 4: OPcache Not Cleared

**Symptom:** PHP still executing old bytecode despite code being updated

**Solution:**
```bash
# Restart PHP-FPM to clear OPcache
sudo systemctl restart php8.4-fpm

# Or via Forge: Site > PHP > Restart
```

---

## Quick Debug Script

Run this all-in-one command:

```bash
cd /home/forge/lift-station.fitness && \
echo "=== Git Status ===" && \
git log -2 --oneline && \
echo -e "\n=== BookingSlotResource Check ===" && \
grep -c "booking.*BookingResource" app/Http/Resources/BookingSlotResource.php && \
echo -e "\n=== Frontend Build ===" && \
ls -lh public/build/manifest.json && \
echo -e "\n=== PHP-FPM Log (last 30 lines) ===" && \
sudo tail -30 /var/log/php8.4-fpm.log && \
echo -e "\n=== Nginx Log (last 30 lines) ===" && \
sudo tail -30 /var/log/nginx/error.log
```

---

## What to Share Back

When you return, share:

1. **Output from PHP-FPM log** (`sudo tail -200 /var/log/php8.4-fpm.log`)
2. **Which endpoint fails** (from curl tests)
3. **Frontend build timestamp** (`ls -lh public/build/manifest.json`)
4. **Forge deployment log** (screenshot or copy/paste)

With that info, we can pinpoint the exact issue!

---

## Expected Behavior After Fix

- ✅ `BookingSlotResource` does NOT include `booking` key
- ✅ Controllers pass `booking` as separate Inertia prop when needed
- ✅ Vue components receive `booking` directly as prop (not nested in `bookingSlot`)
- ✅ No circular references anywhere
- ✅ No 502 errors, all pages load successfully

---

## Emergency Rollback Plan

If needed, you can temporarily revert:

```bash
# Revert to before refactoring
git revert e4734bc c82661b

# Or checkout previous commit
git checkout 9fe62a0  # Before the refactoring

# Then deploy
```

But this brings back the original circular reference issue, so only use as last resort.
