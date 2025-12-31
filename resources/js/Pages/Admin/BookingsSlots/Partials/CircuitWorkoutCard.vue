<template>
    <div @click="$emit('edit')" class="bg-white rounded-lg p-3 space-y-2 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <div class="font-medium text-sm text-zinc-900">{{ workout.name }}</div>
                <div class="flex flex-wrap gap-1 mt-1">
                    <span
                        v-for="category in workout.categories"
                        :key="category"
                        class="text-xs px-2 py-0.5 rounded font-medium"
                        :class="{
                            'bg-blue-50 text-blue-700': category === 'Strength' || category === 'Arms',
                            'bg-amber-100 text-amber-700': category === 'Chest',
                            'bg-pink-50 text-pink-700': category === 'Shoulders',
                            'bg-green-50 text-green-700': category === 'Back',
                            'bg-purple-100 text-purple-700': category === 'Core',
                            'bg-yellow-100 text-yellow-700': category === 'Bodyweight',
                            'bg-lime-100 text-lime-700': category === 'Abs',
                            'bg-red-100 text-red-700': category === 'Legs',
                            'bg-zinc-100 text-zinc-600': !['Strength', 'Chest', 'Shoulders', 'Back', 'Core', 'Bodyweight', 'Abs', 'Legs', 'Arms'].includes(category)
                        }"
                    >
                        {{ category }}
                    </span>
                </div>
            </div>
            <button
                @click="handleDelete"
                class="text-zinc-400 hover:text-red-500 transition-colors cursor-pointer"
                title="Delete workout"
            >
                <TrashIcon class="w-4 h-4 cursor-pointer" />
            </button>
        </div>

        <!-- Sets Display -->
        <div class="space-y-1 pt-2 border-t border-zinc-100">
            <div
                v-for="(set, index) in workout.sets"
                :key="index"
                class="text-xs text-zinc-600 flex items-center gap-2"
            >
                <span class="font-medium text-zinc-500">Set {{ index + 1 }}:</span>
                <span v-if="set.weight_in_kg">
                    {{ set.reps }} reps @ {{ set.weight_in_kg }}kg
                </span>
                <span v-else-if="set.duration_in_seconds">
                    {{ formatDuration(set.duration_in_seconds) }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workout: { type: Object, required: true },
    circuitId: { type: Number, required: true },
    bookingSlotId: { type: Number, required: true },
})

const emit = defineEmits(['deleted', 'edit'])

const handleDelete = (event) => {
    event.stopPropagation() // Prevent triggering edit when deleting
    if (confirm(`Delete "${props.workout.name}" from this circuit?`)) {
        emit('deleted', props.workout.id)
    }
}

const formatDuration = (seconds) => {
    if (!seconds) return '0s'
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    if (mins > 0) {
        return secs > 0 ? `${mins}m ${secs}s` : `${mins}m`
    }
    return `${secs}s`
}
</script>
