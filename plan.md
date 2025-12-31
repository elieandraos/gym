## What's Left To Do 🔨
### 1. Build the Trello-like UI
**BookingSlotsShow.vue - Display circuits:**
- Show circuits grouped by name
- Display workouts within each circuit
- Show sets for each workout (reps, weight, duration)
- Add delete buttons for workouts

**Circuit Management UI (New):**
- Create/edit circuits (with name)
- Drag and drop workouts into circuits
- Reorder workouts within circuits
- Add/edit/delete sets for each workout

**Example Structure:**
```
Circuit 1: Upper Body
  ├── Bench Press (3 sets: 12 reps @ 20kg, 10 reps @ 25kg, 8 reps @ 30kg)
  ├── Shoulder Press (3 sets: 12 reps @ 15kg each)
  └── Lat Pulldown (3 sets: 12 reps @ 30kg each)

Circuit 2: Core
  ├── Plank (3 sets: 30s, 45s, 60s)
  ├── Russian Twist (3 sets: 20 reps each)
  └── Leg Raises (3 sets: 15 reps each)
```

### Tables
- `booking_slot_circuits` - Groups workouts together (e.g., "Upper Body Circuit")
- `booking_slot_circuit_workouts` - Individual workouts within a circuit
- `booking_slot_circuit_workout_sets` - Sets for each workout (reps, weight, duration)

### Relationships
```
BookingSlot (1) → (many) BookingSlotCircuit (1) → (many) BookingSlotCircuitWorkout (1) → (many) BookingSlotCircuitWorkoutSet
                                                              ↓
                                                           Workout (base workout definition)
```

### Example Data Flow
```
BookingSlot #123 (Monday 10am session)
  ├── Circuit #1 "Upper Body"
  │     ├── CircuitWorkout #1 → Workout "Bench Press"
  │     │     ├── Set 1: 12 reps @ 20kg
  │     │     ├── Set 2: 10 reps @ 25kg
  │     │     └── Set 3: 8 reps @ 30kg
  │     └── CircuitWorkout #2 → Workout "Shoulder Press"
  │           ├── Set 1: 12 reps @ 15kg
  │           └── Set 2: 10 reps @ 15kg
  └── Circuit #2 "Core"
        ├── CircuitWorkout #3 → Workout "Plank"
        │     ├── Set 1: 30s
        │     └── Set 2: 45s
        └── CircuitWorkout #4 → Workout "Russian Twist"
              ├── Set 1: 20 reps
              └── Set 2: 20 reps
```

---

## Notes
- Next step: Build the Trello-like UI for circuit management
