<template>
    <AppLayout title="Add Workouts">
        <Container>
            <session-header :booking-slot="bookingSlot"></session-header>

            <div class="space-y-8">
                <SearchWorkouts />
                <WorkoutSearchResults />
                <WorkoutForm />
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SessionHeader from '@/Pages/Admin/BookingsSlots/Partials/SessionHeader.vue'
import SearchWorkouts from './Partials/SearchWorkouts.vue'
import WorkoutSearchResults from './Partials/WorkoutSearchResults.vue'
import WorkoutForm from './Partials/WorkoutForm.vue'
import { ref, provide } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    workouts: { type: Array, required: true },
})

const { id } = props.bookingSlot

const search = ref('')
const selectedWorkouts = ref([])

const form = useForm({
    workouts: [],
})

const createWorkoutSets = (workout) => ({
    ...workout,
    type: 'weight',
    weight_in_kg: ['', '', ''],
    reps: ['12', '12', '12'],
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
        search.value = ''
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

provide('workoutState', {
    search,
    workouts: props.workouts,
    selectedWorkouts,
    dragStart,
    drop,
    remove,
    saveWorkouts,
    form,
    bookingSlotId: id
})
</script>
