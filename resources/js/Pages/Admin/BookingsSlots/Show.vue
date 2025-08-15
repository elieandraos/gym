<template>
    <AppLayout title="Booking">
        <Container>
            <session-header :booking-slot="bookingSlot"></session-header>

            <div v-if="Object.keys(groupedWorkouts).length" class="grid gap-6 md:grid-cols-3">
                <div v-for="(items, category) in groupedWorkouts" :key="category" class="space-y-4">
                    <h3 class="font-semibold text-sm text-zinc-600">{{ category }}</h3>
                    <TransitionGroup name="fade" tag="div" class="space-y-4">
                        <div v-for="workout in items" :key="workout.id" class="bg-white border rounded p-4 space-y-2">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold">{{ workout.name }}</h4>
                                <div class="space-x-2 text-sm">
                                    <Link :href="workout.edit_url">Edit</Link>
                                    <button type="button" @click="removeWorkout(workout)" class="text-sky-500 hover:text-sky-700 font-medium">Remove</button>
                                </div>
                            </div>
                            <ul class="list-disc ml-6">
                                <li v-for="(set, index) in sets" :key="index">
                                    <span v-if="set.weight_in_kg">{{ set.weight_in_kg }} kg</span>
                                    <span v-if="set.reps" class="ml-1">x {{ set.reps }} reps</span>
                                    <span v-if="set.duration_in_seconds" class="ml-1">{{ set.duration_in_seconds }}s</span>
                                </li>
                            </ul>
                        </div>
                    </TransitionGroup>
                </div>
            </div>
            <div v-else>No workout details added yet.</div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { toRefs, computed } from 'vue'

import SessionHeader from '@/Pages/Admin/BookingsSlots/Partials/SessionHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
})

const {
    booking,
    workouts,
} = toRefs(props.bookingSlot)

const { sets } = workouts

const groupedWorkouts = computed(() => {
    const groups = {}
    workouts.value.forEach((w) => {
        const cat = w.category || 'Others'
        if (!groups[cat]) groups[cat] = []
        groups[cat].push(w)
    })
    return groups
})

const removeWorkout = (workout) => {
    router.delete(workout.delete_url, {
        preserveScroll: true,
        onSuccess: () => {
            workouts.value = workouts.value.filter(w => w.id !== workout.id)
        },
    })
}
</script>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
