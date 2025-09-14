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

            <div v-if="isTraining" class="mb-8 bg-sky-50 border border-sky-100 flex flex-col md:flex-row lg:flex-row justify-between rounded-lg py-3 px-4">
                <div class="space-y-1.5">
                    <UpcomingSession :is-training="isTraining" :active-booking="activeBooking" />
                    <ScheduledBooking :scheduled-bookings="scheduledBookings" />
                </div>
                <div class="text-[#71717b]">
                    <LastSessionRecap :active-booking="activeBooking" />
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:justify-between gap-6 md:gap-12">
                <div class="w-full p-4">
                    <user-profile :user="member"></user-profile>
                </div>
                <div class="w-full p-4">
                    <user-contact :user="member"></user-contact>
                </div>
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
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'
import LastSessionRecap from '@/Pages/Admin/Members/Partials/LastSessionRecap.vue'
import {computed} from 'vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking, scheduled_bookings: scheduledBookings } = props.member || {}
const isTraining = computed(() => !!activeBooking)
</script>
