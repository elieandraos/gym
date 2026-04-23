<template>
    <div class="flex flex-col w-full md:w-[30%] md:min-w-[300px] md:flex-shrink-0 md:h-full">
        <!-- Circuit Header (Read-Only) -->
        <div class="bg-zinc-50 rounded-lg p-3">
            <div class="flex items-center justify-between px-2 py-1">
                <span class="font-medium text-zinc-800">{{ circuit.name }}</span>
                <button
                    type="button"
                    @click="$emit('clone-circuit', circuit)"
                    class="p-1 rounded hover:bg-zinc-200 text-zinc-400 hover:text-zinc-600 transition-colors cursor-pointer"
                    title="Clone circuit"
                >
                    <DocumentDuplicateIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Body with Workouts -->
        <div class="bg-zinc-50 rounded-lg p-3 space-y-3 mt-2 flex-1 flex flex-col">
            <!-- Workout Cards (Read-Only) -->
            <ReadOnlyCircuitWorkoutCard
                v-for="workout in circuit.workouts"
                :key="workout.id"
                :workout="workout"
                @clone-workout="$emit('clone-workout', $event)"
            />

            <!-- Empty State -->
            <div v-if="!circuit.workouts || circuit.workouts.length === 0" class="text-center text-zinc-400 text-sm py-4">
                No workouts
            </div>
        </div>
    </div>
</template>

<script setup>
import { DocumentDuplicateIcon } from '@heroicons/vue/24/outline'
import ReadOnlyCircuitWorkoutCard from './ReadOnlyCircuitWorkoutCard.vue'

defineProps({
    circuit: { type: Object, required: true },
})

defineEmits(['clone-circuit', 'clone-workout'])
</script>
