<template>
    <AppLayout title="Booking">
        <Container>
            <page-back-button>Back</page-back-button>

            <div class="flex justify-between items-center pb-6 mb-12 border-b border-zinc-200">
                <div class="grow">
                    <div class="flex gap-x-4">
                        <h1 class="text-xl font-bold text-zinc-950">Session Details</h1>
                        <Badge :type="badge_type">{{ status }}</Badge>
                    </div>
                    <div class="flex gap-x-12 mt-3 text-sm text-zinc-500">
                        <div class="flex gap-x-2">
                            <UsersIcon class="w-4 text-zinc-500"></UsersIcon>
                            <span>
                                <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.members.show', { user: booking.member.id })"> {{ booking.member.name}}</Link>
                                ·
                                <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.trainers.show', { user: booking.trainer.id })"> {{ booking.trainer.name}}</Link>
                            </span>
                        </div>
                        <div class="flex gap-x-2">
                            <ClockIcon class="w-4 text-zinc-500"></ClockIcon>
                            <span>{{ formatted_date }} · {{ start_time }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-x-4">
                    <Link :href="route('admin.bookings-slots.edit', id)">
                        <SecondaryButton>Change date & time</SecondaryButton>
                    </Link>
                    <SecondaryButton @click="showMarkAsACancelledModal = true">Cancel Session</SecondaryButton>
                    <Link :href="route('admin.bookings-slots.workout.create', id)">
                        <PrimaryButton>Add workouts</PrimaryButton>
                    </Link>
                </div>
            </div>

            <div v-if="workouts && workouts.length">
                <TransitionGroup name="fade" tag="div" class="space-y-6">
                    <div v-for="workout in workouts" :key="workout.id" class="border-b pb-4">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold">{{ workout.name }}</h3>
                            <div class="space-x-2">
                                <Link :href="workout.edit_url">Edit</Link>
                                <button type="button" @click="removeWorkout(workout)" class="text-sky-500 hover:text-sky-700 font-medium text-sm">Remove</button>
                            </div>
                        </div>
                    <ul class="list-disc ml-6 mt-2">
                        <li v-for="(set, index) in workout.sets" :key="index">
                            <span v-if="set.weight_in_kg">{{ set.weight_in_kg }} kg</span>
                            <span v-if="set.reps" class="ml-1">x {{ set.reps }} reps</span>
                            <span v-if="set.duration_in_seconds" class="ml-1">{{ set.duration_in_seconds }}s</span>
                        </li>
                    </ul>
                    </div>
                </TransitionGroup>
            </div>
            <div v-else>No workout details added yet.</div>
        </Container>

        <teleport to="#modals">
            <modal v-model="showChangeDateModal">
                <ChangeDateModal :date="date" :start_time="start_time" @close="showChangeDateModal = false"></ChangeDateModal>
            </modal>

            <modal v-model="showMarkAsACancelledModal">
                <MarkAsCancelledModal @close="showMarkAsACancelledModal = false"></MarkAsCancelledModal>
            </modal>
        </teleport>
    </AppLayout>
</template>

<script setup>
import { UsersIcon, ClockIcon } from '@heroicons/vue/24/solid'
import { Link, router } from '@inertiajs/vue3'
import { ref, toRefs } from 'vue'

import Badge from '@/Components/Layout/Badge.vue'
import Container from '@/Components/Layout/Container.vue'
import Modal from '@/Components/Layout/Modal.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import ChangeDateModal from '@/Pages/Admin/BookingsSlots/Partials/ChangeDateModal.vue'
import MarkAsCancelledModal from '@/Pages/Admin/BookingsSlots/Partials/MarkAsCancelledModal.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
})

const {
    id,
    booking,
    formatted_date,
    start_time,
    status,
    badge_type,
    date,
    workouts,
} = toRefs(props.bookingSlot)

const showChangeDateModal = ref(false)
const showMarkAsACancelledModal = ref(false)

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
