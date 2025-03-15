<template>
    <AppLayout title="Booking">
        <Container>
            <page-back-button>Back</page-back-button>

            <div>
                <div class="flex justify-between items-center pb-6 mb-12">
                    <div class="grow">
                        <div class="flex gap-x-4">
                            <h1 class="text-xl font-bold text-zinc-950">Training Details</h1>
                        </div>
                        <div class="flex gap-x-12 mt-3 text-sm text-zinc-500">
                            <div class="flex gap-x-2">
                                <UsersIcon class="w-4 text-zinc-500"></UsersIcon>
                                <span>
                                    <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.users.show', { user: member.id, role: 'Member' })"> {{ member.name}}</Link>
                                    ·
                                    <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.users.show', { user: trainer.id, role: 'Trainer' })"> {{ trainer.name}}</Link>
                                </span>
                            </div>
                            <div class="flex gap-x-2">
                                <ClockIcon class="w-4 text-zinc-500"></ClockIcon>
                                <span>ends on {{ formatted_end_date }}</span>
                            </div>
                            <div class="flex gap-x-2">
                                <BellIcon class="w-4 text-zinc-500"></BellIcon>
                                <span>{{ nb_remaining_sessions }} remaining</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-x-4">

                    </div>
                </div>

                <h3 class="font-semibold text-sm pb-1 mb-1">Schedule</h3>
                <BookingSessions :booking-slots="bookingSlots" :trainer="trainer"></BookingSessions>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { ClockIcon, UsersIcon, BellIcon } from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSessions from '@/Pages/Admin/Bookings/Partials/BookingSessions.vue'

const props = defineProps({
    booking: { type: Object, required: true },
})

const {
    member, trainer, nb_remaining_sessions, bookingSlots, formatted_end_date,
} = props.booking
</script>
