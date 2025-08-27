<template>
    <div v-if="Object.keys(groupedWorkouts).length" class="flex flex-wrap gap-8">
        <div v-for="(items, category) in groupedWorkouts" :key="category" class="min-w-40 flex-1">
            <h3 class="font-semibold text-sm mb-2 text-black">{{ category }}</h3>
            <ul class="space-y-1">
                <li
                    v-for="item in items"
                    :key="item.id"
                    class="p-2 text-zinc-600 bg-stone-50 rounded-lg border border-stone-100 hover:bg-stone-100 cursor-move"
                    draggable="true"
                    @dragstart="dragStart($event, item)"
                >
                    {{ item.name }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { inject, computed } from 'vue'

const { search, workouts, dragStart } = inject('workoutState')

const groupedWorkouts = computed(() => {
    if (search.value.length < 3) {
        return {}
    }

    const filtered = workouts.filter((workout) => {
        return workout.name
            .toLowerCase()
            .includes(search.value.toLowerCase())
    })

    const groups = {}

    for (const workout of filtered) {
        if (!groups[workout.category]) groups[workout.category] = []
        groups[workout.category].push(workout)
    }

    for (const key in groups) {
        groups[key].sort((a, b) => a.name.localeCompare(b.name))
    }

    return groups
})
</script>
