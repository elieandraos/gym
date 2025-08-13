<template>
    <AppLayout title="Profile">
        <Container>
            <div class="flex justify-between items-center pb-4">
                <div class="flex flex-wrap grow items-center gap-4">
                    <img class="h-16 w-16 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl/8 font-bold text-zinc-950">{{ name }}</h1>
                            <check-badge-icon class="w-6 h-6 text-sky-500 inline" v-if="in_house"></check-badge-icon>
                        </div>
                        <div class="mt-1 text-sm text-zinc-500">{{ age }} years old · Member since {{  since }}</div>
                    </div>
                </div>

                <SecondaryButton @click="goToBookingsHistory">View training history</SecondaryButton>
            </div>

            <div v-if="isTraining" class="mb-12">
                <training-status :member="member"></training-status>
            </div>

            <div
                v-if="hasScheduledBooking"
                class="w-full italic bg-amber-50 text-base p-4 mb-16 rounded-lg border border-amber-100 text-amber-800 flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>

                {{ first_name }} has a scheduled booking
            </div>

            <div class="flex flex-col md:flex-row md:justify-between gap-6 md:gap-12">
                <div class="bg-stone-50 w-full p-4 rounded-lg">
                    <user-profile :user="member"></user-profile>
                </div>
                <div class="bg-stone-50 w-full p-4 rounded-lg">
                    <user-contact :user="member"></user-contact>
                </div>
            </div>

        </Container>
    </AppLayout>
</template>

<script setup>
import TrainingStatus from '@/Pages/Admin/Members/Partials/TrainingStatus.vue'
import { CheckBadgeIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const {
    id, active_booking, scheduled_bookings, first_name, name, since, profile_photo_url, in_house, age,
} = props.member

const isTraining = computed(() => !!active_booking)
const hasScheduledBooking = computed( () => scheduled_bookings.length)

const goToBookingsHistory = () => router.visit(route('admin.members.history', { user: id }))
</script>
