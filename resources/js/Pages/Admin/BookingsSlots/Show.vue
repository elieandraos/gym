<template>
    <AppLayout title="Booking">
        <Container>
            <PageHeader :sticky="true">
                <BookingSlotHeader :booking-slot="bookingSlot" :with-menu="true"></BookingSlotHeader>
            </PageHeader>

            <div v-if="Object.keys(groupedWorkouts).length" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
                    <div v-for="(items, category) in groupedWorkouts" :key="category" class="bg-stone-50 border border-stone-100 rounded-lg p-4">
                        <h3 class="font-[500] text-[#71717b] bg-white px-3 py-2 rounded-lg mb-4 text-center">{{ category }}</h3>
                        <TransitionGroup name="fade" tag="div" class="space-y-4">
                            <div v-for="workout in items" :key="workout.id" class="bg-white rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-[600]">{{ workout.name }}</h4>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="confirmRemoveWorkout(workout)" class="text-red-300 hover:text-red-700 p-1.5 hover:bg-red-50 hover:rounded cursor-pointer">
                                            <TrashIcon class="size-4" />
                                        </button>
                                    </div>
                                </div>

                                <div v-if="workout.sets && workout.sets.length" class="space-y-2">
                                    <div v-for="(set, index) in workout.sets" :key="index" class="flex items-center gap-2 text-[#71717b]">
                                        <span v-if="set.weight_in_kg">{{ set.weight_in_kg }} kg</span>
                                        <span v-if="set.reps && set.reps > 1">× {{ set.reps }}</span>
                                        <span v-if="set.duration_in_seconds">{{ set.duration_in_seconds }} seconds</span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500">
                                    No sets recorded
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>
            </div>
            <div v-else>No workout details added yet.</div>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/Components/Layout/PageHeader.vue'
import { router } from '@inertiajs/vue3'
import { toRefs, computed, ref } from 'vue'
import { TrashIcon } from '@heroicons/vue/24/solid'

import BookingSlotHeader from '@/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
})

const { workouts } = toRefs(props.bookingSlot)

const removingWorkout = ref(null)

const groupedWorkouts = computed(() => {
    const groups = {}
    workouts.value.forEach((w) => {
        const cat = w.category || 'Others'
        if (!groups[cat]) groups[cat] = []
        groups[cat].push(w)
    })
    return groups
})

const confirmRemoveWorkout = (workout) => {
    const { delete_url } = workout

    if (confirm(`Are you sure you want to remove "${workout.name}" from this session?`)) {
        removingWorkout.value = workout.id
        router.delete(delete_url, {
            preserveScroll: true,
            onSuccess: () => {
                workouts.value = workouts.value.filter(w => w.id !== workout.id)
                removingWorkout.value = null
            },
            onError: () => {
                removingWorkout.value = null
            }
        })
    }
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
