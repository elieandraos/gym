# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

```bash
# Frontend development
npm run dev              # Start Vite dev server
npm run build           # Build for production
npm run eslint          # Run ESLint for JS/Vue files

# Backend development  
php artisan serve       # Start Laravel dev server
php artisan test        # Run all tests (using Pest)
./vendor/bin/pint       # Run Laravel Pint code formatter

# Database
php artisan migrate     # Run migrations
php artisan db:seed     # Run seeders
php artisan tinker      # Laravel REPL
```

## Architecture Overview

### Tech Stack
- **Backend**: Laravel 11 with Jetstream (without API, registration, 2FA, email verification)
- **Frontend**: Inertia.js + Vue 3 + Tailwind CSS 4
- **Testing**: Pest PHP testing framework
- **Database**: MySQL

### Domain Model
This is a gym management system centered around booking training sessions:

**Core Entities:**
- `User` - Members and trainers (role-based via `is_trainer` boolean)
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

### Application Structure

**Controllers (Admin namespace):**
- `MembersController` / `TrainersController` - User management
- `BookingsController` - Create training packages
- `BookingSlotsController` - Individual session management  
- `BookingSlotWorkoutController` - Add/edit workouts in sessions
- `ChangeBookingSlotDateTimeController` - Reschedule sessions
- `CancelBookingSlotController` - Cancel sessions
- `WeeklyCalendarController` - Calendar view

**Frontend Pages Structure:**
- `/resources/js/Pages/Admin/` - All admin interfaces
- Common patterns: Index (lists), Show (details), Create/Edit (forms)
- Partials for reusable components

**Key Vue Components:**
- `SessionHeader` - Session info display
- `MembersList` / `TrainersList` - User listings
- `BookingSchedule` / `RepeatableScheduler` - Booking creation
- Form components in `/Components/Form/`
- Layout components in `/Components/Layout/`

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
