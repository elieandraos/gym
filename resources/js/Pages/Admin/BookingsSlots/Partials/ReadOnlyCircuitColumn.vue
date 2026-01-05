<template>
    <div class="flex flex-col w-full md:w-[30%] md:min-w-[300px] md:flex-shrink-0 md:h-full">
        <!-- Circuit Header (Read-Only) -->
        <div class="bg-zinc-50 rounded-lg p-3">
            <div class="px-2 py-1 font-medium text-zinc-800">
                {{ circuit.name }}
            </div>
        </div>

        <!-- Body with Workouts -->
        <div class="bg-zinc-50 rounded-lg p-3 space-y-3 mt-2 flex-1 flex flex-col">
            <!-- Workout Cards (Read-Only) -->
            <ReadOnlyCircuitWorkoutCard
                v-for="workout in circuit.workouts"
                :key="workout.id"
                :workout="workout"
            />

            <!-- Empty State -->
            <div v-if="!circuit.workouts || circuit.workouts.length === 0" class="text-center text-zinc-400 text-sm py-4">
                No workouts
            </div>
        </div>
    </div>
</template>

<script setup>
import ReadOnlyCircuitWorkoutCard from './ReadOnlyCircuitWorkoutCard.vue'

defineProps({
    circuit: { type: Object, required: true },
})
</script>