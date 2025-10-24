<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-12">
                <TrainingStatusWidget :is-training="isTraining" :active-booking="activeBooking" />
                <PaymentStatusWidget :member="member" />
                <RemainingSessionsWidget :is-training="isTraining" :active-booking="activeBooking" />
            </div>

            <div class="w-full p-4">
                <h3 class="font-[600] mb-4">Overview</h3>
                <table class="text-left text-sm w-full">
                    <tbody>
                        <tr class="border-b border-zinc-100">
                            <td class="text-[#71717b] py-4">Upcoming Session</td>
                            <td class="py-4">
                                <UpcomingSession :is-training="isTraining" :active-booking="activeBooking" />
                            </td>
                        </tr>
                        <tr class="border-b border-zinc-100">
                            <td class="text-[#71717b] py-4">Last workouts</td>
                            <td class="py-4">
                                <LastSessionRecap :active-booking="activeBooking" />
                            </td>
                        </tr>
                        <tr class="border-b border-zinc-100">
                            <td class="text-[#71717b] py-4">Scheduled Trainings</td>
                            <td class="py-4">
                                <ScheduledBooking :scheduled-bookings="scheduledBookings" />
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#71717b] py-4">Last body composition</td>
                            <td class="py-4">
                                <a v-if="lastBodyComposition" :href="lastBodyComposition.photo_url" target="_blank" class="text-sky-500 hover:text-sky-700 font-[400]">
                                    {{ lastBodyComposition.taken_at_formatted }}
                                </a>
                                <span v-else class="text-zinc-400">
                                    <Link :href="route('admin.members.body-composition.create', { user: member.id })" class="text-sky-500 hover:text-sky-700 font-[400]">
                                        Start tracking
                                    </Link>
                                    {{ member.name.split(' ')[0] }}'s transformation journey!
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import TrainingStatusWidget from '@/Pages/Admin/Members/Partials/TrainingStatusWidget.vue'
import ScheduledBooking from '@/Pages/Admin/Members/Partials/ScheduledBooking.vue'
import PaymentStatusWidget from '@/Pages/Admin/Members/Partials/PaymentStatusWidget.vue'
import UpcomingSession from '@/Pages/Admin/Members/Partials/UpcomingSession.vue'
import RemainingSessionsWidget from '@/Pages/Admin/Members/Partials/RemainingSessionsWidget.vue'
import LastSessionRecap from '@/Pages/Admin/Members/Partials/LastSessionRecap.vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking, scheduled_bookings: scheduledBookings, last_body_composition: lastBodyComposition } = props.member || {}
const isTraining = computed(() => !!activeBooking)
</script>
