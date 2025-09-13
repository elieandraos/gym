<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <TrainingStatusWidget :member="member" />
                <PaymentStatusWidget :member="member" />
                <RemainingSessionsWidget :member="member" />
            </div>

            <UpcomingSessionItem :member="member" />
            <ScheduledBookingItem :member="member" />

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
import ScheduledBookingItem from '@/Pages/Admin/Members/Partials/ScheduledBookingItem.vue'
import PaymentStatusWidget from '@/Pages/Admin/Members/Partials/PaymentStatusWidget.vue'
import UpcomingSessionItem from '@/Pages/Admin/Members/Partials/UpcomingSessionItem.vue'
import RemainingSessionsWidget from '@/Pages/Admin/Members/Partials/RemainingSessionsWidget.vue'
import Container from '@/Components/Layout/Container.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const props = defineProps({
    member: { type: Object, required: true },
})
</script>
