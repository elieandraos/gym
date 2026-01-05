<template>
    <div @click="$emit('edit')" class="bg-white rounded-lg p-3 space-y-2 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <div class="font-medium text-sm text-zinc-900">{{ workout.name }}</div>
                <div v-if="workout.categories && workout.categories.length" class="flex flex-wrap gap-1 mt-1">
                    <span
                        v-for="category in workout.categories"
                        :key="category"
                        class="text-xs px-2 py-0.5 rounded font-medium"
                        :class="getCategoryClass(category)"
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
import { router } from '@inertiajs/vue3'
import { TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workout: { type: Object, required: true },
    circuitId: { type: Number, required: true },
    bookingSlotId: { type: Number, required: true },
})

const emit = defineEmits(['edit'])

const getCategoryClass = (category) => {
    const classes = {
        'Strength': 'bg-blue-50 text-blue-700',
        'Arms': 'bg-blue-50 text-blue-700',
        'Chest': 'bg-amber-100 text-amber-700',
        'Shoulders': 'bg-pink-50 text-pink-700',
        'Back': 'bg-green-50 text-green-700',
        'Core': 'bg-purple-100 text-purple-700',
        'Bodyweight': 'bg-yellow-100 text-yellow-700',
        'Abs': 'bg-lime-100 text-lime-700',
        'Legs': 'bg-red-100 text-red-700',
    }
    return classes[category] || 'bg-blue-50 text-blue-700'
}

const handleDelete = (event) => {
    event.stopPropagation() // Prevent triggering edit when deleting
    if (confirm(`Delete "${props.workout.name}" from this circuit?`)) {
        router.delete(
            route('admin.bookings-slots.circuits.workouts.destroy', {
                bookingSlot: props.bookingSlotId,
                circuit: props.circuitId,
                circuitWorkout: props.workout.id,
            }),
            { preserveScroll: true }
        )
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
