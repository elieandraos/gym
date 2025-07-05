<template>
    <AppLayout title="Add Workouts">
        <Container>
            <page-back-button :url="route('admin.bookings-slots.show', id)">Back</page-back-button>

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="lg:w-1/3 space-y-6">
                    <div>
                        <InputLabel value="Search" />
                        <TextInput v-model="search" placeholder="Search workouts..." class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Categories" class="mt-4" />
                        <div class="space-y-1 mt-1">
                            <label v-for="category in categories" :key="category" class="flex items-center gap-2">
                                <Checkbox v-model:checked="selectedCategories" :value="category" />
                                <span class="text-sm text-gray-700">{{ category }}</span>
                            </label>
                        </div>
                    </div>

                    <div v-for="(items, category) in groupedWorkouts" :key="category">
                        <h3 class="font-semibold text-sm mb-2 text-zinc-600">{{ category }}</h3>
                        <ul class="space-y-1">
                            <li
                                v-for="item in items"
                                :key="item.id"
                                class="p-2 bg-stone-50 rounded border border-stone-100 hover:bg-stone-100 cursor-move"
                                draggable="true"
                                @dragstart="dragStart($event, item)"
                            >
                                {{ item.name }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="lg:w-2/3 space-y-4">
                    <h3 class="font-semibold text-sm mb-2 text-zinc-600">Selected Workouts</h3>
                    <div
                        class="min-h-40 p-4 bg-stone-50 border border-stone-100 rounded"
                        @dragover.prevent
                        @drop="drop"
                    >
                        <ul class="space-y-2">
                            <li
                                v-for="(selectedWorkout, workoutIndex) in selectedWorkouts"
                                :key="selectedWorkout.id"
                                class="flex justify-between items-center bg-white border p-2 rounded"
                            >
                                <span>{{ selectedWorkout.name }}</span>
                                <TransparentButton type="button" @click="remove(workoutIndex)">Remove</TransparentButton>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    workouts: { type: Array, required: true },
})

const { id } = props.bookingSlot
const { route } = window

const search = ref('')
const selectedCategories = ref([])

const categories = computed(() => {
    const list = []
    for (const workout of props.workouts) {
        if (!list.includes(workout.category)) list.push(workout.category)
    }
    return list
})

const groupedWorkouts = computed(() => {
    if (!search.value) {
        return {}
    }
    const filtered = props.workouts.filter((workout) => {
        const matchesSearch = workout.name
            .toLowerCase()
            .includes(search.value.toLowerCase())
        const matchesCategory =
            selectedCategories.value.length === 0 ||
            selectedCategories.value.includes(workout.category)
        return matchesSearch && matchesCategory
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

const selectedWorkouts = ref([])

const dragStart = (event, workout) => {
    event.dataTransfer.setData('workout', JSON.stringify(workout))
}

const drop = (event) => {
    const data = event.dataTransfer.getData('workout')
    if (!data) return
    const workout = JSON.parse(data)
    if (!selectedWorkouts.value.some((existingWorkout) => existingWorkout.id === workout.id)) {
        selectedWorkouts.value.push(workout)
    }
}

const remove = (workoutIndex) => {
    selectedWorkouts.value.splice(workoutIndex, 1)
}
</script>
