<template>
    <div>
        <button
            @click="showModal = true"
            class="w-full py-2 px-3 text-sm text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 rounded-lg transition-colors flex items-center justify-center gap-2 border border-dashed border-zinc-300 cursor-pointer"
        >
            <PlusIcon class="w-4 h-4 cursor-pointer" />
            <span>Add Workout</span>
        </button>

        <!-- Add Workout Modal -->
        <AddWorkoutModal
            :show="showModal"
            :circuit-id="circuitId"
            :booking-slot-id="bookingSlotId"
            :available-workouts="availableWorkouts"
            @close="showModal = false"
            @workout-added="handleWorkoutAdded"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/outline'
import AddWorkoutModal from './AddWorkoutModal.vue'

defineProps({
    circuitId: { type: Number, required: true },
    bookingSlotId: { type: Number, required: true },
    availableWorkouts: { type: Array, required: true },
})

const emit = defineEmits(['workout-added'])

const showModal = ref(false)

const handleWorkoutAdded = (workout) => {
    emit('workout-added', workout)
    showModal.value = false
}
</script>
