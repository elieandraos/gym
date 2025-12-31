<template>
    <AppLayout title="Booking">
        <Container>
            <PageHeader :sticky="true">
                <BookingSlotHeader :booking-slot="bookingSlot" :booking-id="bookingId" :with-menu="true"></BookingSlotHeader>
            </PageHeader>

            <!-- Trello-style Circuit Board -->
            <div class="flex flex-col md:flex-row gap-4 md:overflow-x-auto pb-4 px-4 mt-6">
                <!-- Add Circuit Button - Shows first on mobile, last on desktop -->
                <div class="md:hidden">
                    <AddCircuitButton
                        :booking-slot-id="bookingSlot.id"
                        @circuit-added="handleCircuitAdded"
                    />
                </div>

                <!-- Circuit Columns -->
                <CircuitColumn
                    v-for="circuit in circuits"
                    :key="circuit.id"
                    :circuit="circuit"
                    :booking-slot-id="bookingSlot.id"
                    :available-workouts="availableWorkouts"
                    @circuit-updated="handleCircuitUpdate"
                    @circuit-deleted="handleCircuitDelete"
                    @workout-added="handleWorkoutAdded"
                    @workout-deleted="handleWorkoutDeleted"
                    @workout-updated="handleWorkoutUpdated"
                />

                <!-- Add Circuit Button - Desktop only -->
                <div class="hidden md:block">
                    <AddCircuitButton
                        :booking-slot-id="bookingSlot.id"
                        @circuit-added="handleCircuitAdded"
                    />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'

import BookingSlotHeader from '@/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CircuitColumn from '@/Pages/Admin/BookingsSlots/Partials/CircuitColumn.vue'
import AddCircuitButton from '@/Pages/Admin/BookingsSlots/Partials/AddCircuitButton.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    bookingId: { type: [Number, String], default: null },
})

// Dummy workouts data for search
const availableWorkouts = ref([
    { id: 1, name: 'Bench Press', categories: ['Strength', 'Chest'] },
    { id: 2, name: 'Shoulder Press', categories: ['Strength', 'Shoulders'] },
    { id: 3, name: 'Lat Pulldown', categories: ['Strength', 'Back'] },
    { id: 4, name: 'Plank', categories: ['Core', 'Bodyweight'] },
    { id: 5, name: 'Russian Twist', categories: ['Core', 'Abs'] },
    { id: 6, name: 'Leg Raises', categories: ['Core', 'Abs'] },
    { id: 7, name: 'Squats', categories: ['Strength', 'Legs'] },
    { id: 8, name: 'Deadlift', categories: ['Strength', 'Back', 'Legs'] },
    { id: 9, name: 'Bicep Curls', categories: ['Strength', 'Arms'] },
    { id: 10, name: 'Tricep Dips', categories: ['Strength', 'Arms'] },
])

// Initialize with empty circuits or dummy data
const circuits = ref([])

// Circuit management handlers
const handleCircuitAdded = (newCircuit) => {
    circuits.value.push(newCircuit)
}

const handleCircuitUpdate = (updatedCircuit) => {
    const index = circuits.value.findIndex(c => c.id === updatedCircuit.id)
    if (index !== -1) {
        circuits.value[index] = updatedCircuit
    }
}

const handleCircuitDelete = (circuitId) => {
    circuits.value = circuits.value.filter(c => c.id !== circuitId)
}

// Workout management handlers
const handleWorkoutAdded = ({ circuitId, workout }) => {
    const circuit = circuits.value.find(c => c.id === circuitId)
    if (circuit) {
        if (!circuit.workouts) {
            circuit.workouts = []
        }
        circuit.workouts.push(workout)
    }
}

const handleWorkoutDeleted = ({ circuitId, workoutId }) => {
    const circuit = circuits.value.find(c => c.id === circuitId)
    if (circuit && circuit.workouts) {
        circuit.workouts = circuit.workouts.filter(w => w.id !== workoutId)
    }
}

const handleWorkoutUpdated = ({ circuitId, workout }) => {
    const circuit = circuits.value.find(c => c.id === circuitId)
    if (circuit && circuit.workouts) {
        const index = circuit.workouts.findIndex(w => w.id === workout.id)
        if (index !== -1) {
            circuit.workouts[index] = workout
        }
    }
}
</script>
