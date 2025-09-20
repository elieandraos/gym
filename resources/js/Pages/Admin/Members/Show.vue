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
                            <td class="text-[#71717b] py-4">Recent workouts</td>
                            <td class="py-4">
                                <LastSessionRecap :active-booking="activeBooking" />
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#71717b] py-4">Scheduled Trainings</td>
                            <td class="py-4">
                                <ScheduledBooking :scheduled-bookings="scheduledBookings" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import TrainingStatusWidget from '@/Pages/Admin/Members/Partials/TrainingStatusWidget.vue'
import ScheduledBooking from '@/Pages/Admin/Members/Partials/ScheduledBooking.vue'
import PaymentStatusWidget from '@/Pages/Admin/Members/Partials/PaymentStatusWidget.vue'
import UpcomingSession from '@/Pages/Admin/Members/Partials/UpcomingSession.vue'
import RemainingSessionsWidget from '@/Pages/Admin/Members/Partials/RemainingSessionsWidget.vue'
import Container from '@/Components/Layout/Container.vue'
import LastSessionRecap from '@/Pages/Admin/Members/Partials/LastSessionRecap.vue'
import {computed} from 'vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking, scheduled_bookings: scheduledBookings } = props.member || {}
const isTraining = computed(() => !!activeBooking)
</script>
