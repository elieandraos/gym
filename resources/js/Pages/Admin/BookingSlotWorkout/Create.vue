<template>
    <AppLayout title="Add Workouts">
        <Container>
            <session-header :booking-slot="bookingSlot"></session-header>

            <div class="space-y-8">
                <div>
                    <TextInput v-model="search" placeholder="Search workouts..." class="w-full" />
                </div>

                <div v-if="Object.keys(groupedWorkouts).length" class="flex flex-wrap gap-8">
                    <div v-for="(items, category) in groupedWorkouts" :key="category" class="min-w-40 flex-1">
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

                <div class="space-y-4">
                    <div
                        class="min-h-40 p-4 bg-stone-50 border border-stone-100 rounded"
                        @dragover.prevent
                        @drop="drop"
                    >
                        <ul class="space-y-2">
                            <li
                                v-for="(selectedWorkout, workoutIndex) in selectedWorkouts"
                                :key="selectedWorkout.id"
                                class="bg-white border p-2 rounded space-y-2"
                            >
                                <div class="flex justify-between items-center">
                                    <span>{{ selectedWorkout.name }}</span>
                                    <TransparentButton type="button" @click="remove(workoutIndex)">Remove</TransparentButton>
                                </div>

                                <div class="flex items-center gap-4 text-sm">
                                    <label class="flex items-center gap-1">
                                        <input
                                            type="radio"
                                            :name="'type-' + workoutIndex"
                                            value="weight"
                                            v-model="selectedWorkout.type"
                                        />
                                        Weight
                                    </label>
                                    <label class="flex items-center gap-1">
                                        <input
                                            type="radio"
                                            :name="'type-' + workoutIndex"
                                            value="seconds"
                                            v-model="selectedWorkout.type"
                                        />
                                        Seconds
                                    </label>
                                </div>

                                <div v-if="selectedWorkout.type === 'weight'" class="flex gap-2">
                                    <div class="flex items-center gap-1" v-for="(value, idx) in selectedWorkout.weight_in_kg" :key="idx">
                                        <TextInput
                                            v-model="selectedWorkout.weight_in_kg[idx]"
                                            name="weight_in_kg[]"
                                            type="number"
                                            placeholder="KG"
                                            class="w-16"
                                        />
                                        <TextInput
                                            v-model="selectedWorkout.reps[idx]"
                                            name="reps[]"
                                            type="number"
                                            placeholder="Reps"
                                            class="w-16"
                                        />
                                    </div>
                                </div>

                                <div v-if="selectedWorkout.type === 'seconds'" class="flex gap-2">
                                    <TextInput
                                        v-for="(value, idx) in selectedWorkout.duration_in_seconds"
                                        :key="idx"
                                        v-model="selectedWorkout.duration_in_seconds[idx]"
                                        name="duration_in_seconds[]"
                                        type="number"
                                        placeholder="Seconds"
                                        class="w-16"
                                    />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="text-right space-x-4">
                    <Link :href="route('admin.bookings-slots.show', id)">
                        <TransparentButton>Cancel</TransparentButton>
                    </Link>
                    <PrimaryButton @click="saveWorkouts" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save Workouts
                    </PrimaryButton>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import TextInput from '@/Components/Form/TextInput.vue'

import Container from '@/Components/Layout/Container.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SessionHeader from '@/Pages/Admin/BookingsSlots/Partials/SessionHeader.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    workouts: { type: Array, required: true },
})

const { id } = props.bookingSlot
const { route } = window

const search = ref('')

const form = useForm({
    workouts: [],
})

const groupedWorkouts = computed(() => {
    if (search.value.length < 3) {
        return {}
    }

    const filtered = props.workouts.filter((workout) => {
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

const selectedWorkouts = ref([])

const createWorkoutSets = (workout) => ({
    ...workout,
    type: 'weight',
    weight_in_kg: ['', '', ''],
    reps: ['', '', ''],
    duration_in_seconds: ['', '', ''],
})

const dragStart = (event, workout) => {
    event.dataTransfer.setData('workout', JSON.stringify(workout))
}

const drop = (event) => {
    const data = event.dataTransfer.getData('workout')
    if (!data) return
    const workout = JSON.parse(data)
    if (!selectedWorkouts.value.some((existingWorkout) => existingWorkout.id === workout.id)) {
        selectedWorkouts.value.push(createWorkoutSets(workout))
    }
}

const remove = (workoutIndex) => {
    selectedWorkouts.value.splice(workoutIndex, 1)
}

const saveWorkouts = () => {
    form.workouts = selectedWorkouts.value
    form.post(route('admin.bookings-slots.workout.store', id), {
        preserveScroll: true,
    })
}
</script>
