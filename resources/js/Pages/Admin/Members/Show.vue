<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-12">
                <TrainingStatusWidget :member="member" />
                <PaymentStatusWidget :member="member" />
                <RemainingSessionsWidget :member="member" />
            </div>

            <Banner class="mb-8 flex justify-between items-center gap-6">
                <div class="space-y-1.5">
                    <UpcomingSessionItem :member="member" />
                    <ScheduledBookingItem :member="member" />
                </div>
                <div class="text-[#71717b]">
                    <!-- @todo: add last (up to 3) sessions workouts day and  category.
                    for ex: Mon: Biceps,Back  · Wed:Legs,Core,Triceps · Fri: Shoulders,Back
                    have its own component named 'LastSessionRecap'
                    -->
                </div>
            </Banner>

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
import Banner from '@/Components/Banner.vue'
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
