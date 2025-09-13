<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="bg-white rounded-lg mb-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <TrainingStatusItem :member="member" />
                    <PaymentStatusItem :member="member" />
                    <TrainerItem :member="member" />
                    <RemainingSessionsItem :member="member" />
                </div>
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
import TrainingStatusItem from '@/Pages/Admin/Members/Partials/TrainingStatusItem.vue'
import ScheduledBookingItem from '@/Pages/Admin/Members/Partials/ScheduledBookingItem.vue'
import PaymentStatusItem from '@/Pages/Admin/Members/Partials/PaymentStatusItem.vue'
import TrainerItem from '@/Pages/Admin/Members/Partials/TrainerItem.vue'
import UpcomingSessionItem from '@/Pages/Admin/Members/Partials/UpcomingSessionItem.vue'
import RemainingSessionsItem from '@/Pages/Admin/Members/Partials/RemainingSessionsItem.vue'
import Container from '@/Components/Layout/Container.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const props = defineProps({
    member: { type: Object, required: true },
})
</script>
