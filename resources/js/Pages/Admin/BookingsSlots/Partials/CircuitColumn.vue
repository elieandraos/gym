<template>
    <div class="flex flex-col w-full md:w-[30%] md:min-w-[300px] md:flex-shrink-0 md:h-full">
        <!-- Detached Header -->
        <CircuitHeader
            :circuit="circuit"
            :booking-slot-id="bookingSlotId"
        />

        <!-- Body with Workouts -->
        <div class="bg-zinc-50 rounded-lg p-3 space-y-3 mt-2 flex-1 flex flex-col">
            <!-- Workout Cards -->
            <CircuitWorkoutCard
                v-for="workout in circuit.workouts"
                :key="workout.id"
                :workout="workout"
                :circuit-id="circuit.id"
                :booking-slot-id="bookingSlotId"
                @edit="handleWorkoutEdit(workout)"
            />

            <!-- Add Workout Button -->
            <div class="mt-auto">
                <AddWorkoutButton
                    :circuit-id="circuit.id"
                    :booking-slot-id="bookingSlotId"
                    :available-workouts="availableWorkouts"
                />
            </div>
        </div>

        <!-- Edit Workout Modal -->
        <AddWorkoutModal
            :show="showEditModal"
            :circuit-id="circuit.id"
            :booking-slot-id="bookingSlotId"
            :available-workouts="availableWorkouts"
            :editing-workout="editingWorkout"
            @close="showEditModal = false; editingWorkout = null"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import CircuitHeader from './CircuitHeader.vue'
import CircuitWorkoutCard from './CircuitWorkoutCard.vue'
import AddWorkoutButton from './AddWorkoutButton.vue'
import AddWorkoutModal from './AddWorkoutModal.vue'

const props = defineProps({
    circuit: { type: Object, required: true },
    bookingSlotId: { type: Number, required: true },
    availableWorkouts: { type: Array, required: true },
})

const showEditModal = ref(false)
const editingWorkout = ref(null)

const handleWorkoutEdit = (workout) => {
    editingWorkout.value = workout
    showEditModal.value = true
}
</script>
