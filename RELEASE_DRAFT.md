# Release Notes

This release introduces a circuit-based workout management system with a Trello-style interface, replacing the previous flat workout list. Trainers can now organize exercises into logical circuits within each training session, providing better structure and reflecting real-world training methodologies.

## 🏋️ Circuit-Based Workout System

### Overview

The workout management system has been redesigned around training circuits—groups of exercises performed together during a session. This replaces the flat workout list with a hierarchical structure.

**Previous Structure:**
```
BookingSlot → Workouts[] (flat list)
```

**New Structure:**
```
BookingSlot → Circuits[] → Workouts[] → Sets[]
```

### Key Features

**Trello-Style Interface:**
- Visual column-based layout with horizontal scrolling
- Each circuit displayed as a vertical column
- Workout cards within each circuit
- Responsive design (vertical on mobile, horizontal on desktop)
- "Add Circuit" button for creating new workout groups

**Circuit Management:**
- Create unlimited circuits per session
- Auto-generated names: "Circuit 1", "Circuit 2", etc.
- Rename circuits: "Upper Body Circuit", "Core Circuit", "Cardio Circuit"
- Delete entire circuits (removes all workouts/sets)
- Inline click-to-edit functionality

**Workout Organization:**
- Add workouts to specific circuits via modal
- Workout cards display:
    - Exercise name with color-coded category badges
    - All sets (reps/weight or duration)
    - Edit and delete actions
- Search workouts with autocomplete
- Click any card to edit

**Flexible Set Configuration:**
- **Weight-based workouts**: Reps + Weight (e.g., "12 reps @ 25kg")
- **Duration-based workouts**: Time only (e.g., "30s plank")
- Radio button type selection
- Add/remove sets dynamically (1-10 sets)
- Clean display without "Set 1:", "Set 2:" labels

### Database Changes

**New Tables:**
- `booking_slot_circuits` - Circuit groups
- `booking_slot_circuit_workouts` - Workouts within circuits
- `booking_slot_circuit_workout_sets` - Individual sets (reps/weight/duration)

**Removed Tables:**
- `booking_slot_workouts` (replaced by circuit workouts)
- `workout_sets` (replaced by circuit workout sets)

### Backend Implementation

**New Controllers:**
- `BookingSlotCircuitsController` - CRUD operations for circuits
- `BookingSlotCircuitWorkoutsController` - Manage workouts within circuits

**New Models:**
- `BookingSlotCircuit` - Circuit model with relationships
- `BookingSlotCircuitWorkout` - Circuit workout model
- `BookingSlotCircuitWorkoutSet` - Set model with nullable reps

**Validation:**
- Circuit name validation
- Workout type validation (weight/duration)
- Sets array validation with reps/weight/duration checks

### Frontend Components

**New Components:**
- `CircuitColumn.vue` - Circuit display container
- `CircuitHeader.vue` - Circuit title with inline editing
- `CircuitWorkoutCard.vue` - Workout card with sets display
- `AddWorkoutModal.vue` - Create/edit workout modal
- `AddWorkoutButton.vue` - Trigger for adding workouts
- `AddCircuitButton.vue` - Trigger for creating circuits

**Updated Components:**
- `Show.vue` - Redesigned as Trello-style board
- Enhanced responsive design for mobile/desktop

### Testing

**15 Comprehensive Tests (All Passing):**
- Circuit CRUD operations
- Workout creation (weight-based and duration-based)
- Validation testing
- Authentication requirements
- Cascading delete verification

Test file: `tests/Feature/Admin/BookingSlotCircuitTest.php`

## 🎨 UI/UX Improvements

### Form Styling Consistency

**Black Accent Theme:**
- Updated all checkboxes to use black accent (workout category selection)
- Updated all radio buttons to use black accent (Weight/Duration type)
- Replaced Jetstream's default indigo with black for consistency
- Better visual hierarchy and professional appearance

### Cleaner Workout Display
**Color-Coded Categories:**
- Category badges with distinct colors
- Chest (amber), Back (green), Legs (red), etc.
- Better visual identification

## 🗃️ Database Schema Updates

### Nullable Reps Column

**Change:** Made `reps` column nullable in `booking_slot_circuit_workout_sets` table

**Reason:**
- Duration-based exercises (planks, holds) don't use reps
- Previous schema forced meaningless default values

**Solution:**
- Weight workouts: store reps + weight
- Duration workouts: store duration only (reps = null)

**Migration Consolidation:**
- Merged nullable change into original table creation migration
- Cleaner migration history

## 🔧 Technical Improvements

**Route Organization:**
- Added nested resource routes for circuits and circuit workouts
- RESTful API structure
- Proper route naming conventions

**Seeder Updates:**
- `BookingSlotCircuitWorkoutSeeder` - Creates realistic circuit data
- 1-2 circuits per completed session
- Random circuit names and workout selection
- 70% weight-based, 30% duration-based workouts
- Fixed bug: Updated JSON query from `where('category')` to `whereJsonContains('categories')`

**Code Cleanup:**
- Removed `BookingSlotWorkoutSeeder` (obsolete)
- All code formatted with Laravel Pint
- Comprehensive type hints and validation
- Cascading deletes for data integrity

## 🎯 Breaking Changes

**For Developers:**
- Removed `BookingSlotWorkout` model → Use `BookingSlotCircuitWorkout`
- Removed `BookingSlotWorkoutSet` model → Use `BookingSlotCircuitWorkoutSet`
- Removed `BookingSlotWorkoutController` → Use circuit controllers
- Removed `BookingSlotWorkoutSeeder` → Use `BookingSlotCircuitWorkoutSeeder`
- New relationship: `BookingSlot::circuits()`
- Routes changed from `booking-slot-workout.*` to `bookings-slots.circuits.*`

## Summary

This release transforms workout management with a circuit-based system and Trello-style interface. Trainers can now organize workouts into logical groups that reflect real-world training methodologies. The nullable reps column enables proper support for both weight-based and duration-based exercises. UI improvements include consistent black accent styling and cleaner workout displays. All changes maintain backward compatibility for end users while providing a modern, intuitive interface backed by comprehensive test coverage.
