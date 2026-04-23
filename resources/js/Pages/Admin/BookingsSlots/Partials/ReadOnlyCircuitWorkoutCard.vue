<template>
    <div class="bg-white rounded-lg p-3 space-y-2 shadow-sm">
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
                type="button"
                @click="$emit('clone-workout', workout)"
                class="ml-2 flex-shrink-0 p-1 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-600 transition-colors cursor-pointer"
                title="Clone workout"
            >
                <DocumentDuplicateIcon class="w-4 h-4" />
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
import { DocumentDuplicateIcon } from '@heroicons/vue/24/outline'

defineProps({
    workout: { type: Object, required: true },
})

defineEmits(['clone-workout'])

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
