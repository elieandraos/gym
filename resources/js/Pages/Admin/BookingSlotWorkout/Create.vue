<template>
    <AppLayout title="Add Workouts">
        <Container>
            <PageHeader :sticky="true">
                <BookingSlotHeader :booking-slot="bookingSlot"></BookingSlotHeader>
            </PageHeader>

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
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSlotHeader from '@/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue'
import SearchWorkouts from './Partials/SearchWorkouts.vue'
import WorkoutSearchResults from './Partials/WorkoutSearchResults.vue'
import WorkoutForm from './Partials/WorkoutForm.vue'
import { ref, provide, watch } from 'vue'
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
        selectedWorkouts.value.unshift(createWorkoutSets(workout))
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

// Watch for type changes and update reps accordingly
watch(
    () => selectedWorkouts.value.map(w => ({ id: w.id, type: w.type })),
    (newWorkouts, oldWorkouts) => {
        selectedWorkouts.value.forEach((workout) => {
            const oldWorkout = oldWorkouts?.find(w => w.id === workout.id)
            if (oldWorkout && oldWorkout.type !== workout.type) {
                if (workout.type === 'seconds') {
                    workout.reps = ['1', '1', '1']
                } else if (workout.type === 'weight') {
                    workout.reps = ['12', '12', '12']
                }
            }
        })
    },
    { deep: true }
)

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
