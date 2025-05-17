<template>
    <AppLayout title="Profile">
        <Container>
            <page-back-button>Members</page-back-button>

            <div class="flex justify-between items-center pb-6">
                <div class="flex flex-wrap grow items-center gap-6">
                    <img class="h-20 w-20 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-bold text-zinc-950">{{ name }}</h1>
                            <check-badge-icon class="w-6 h-6 text-sky-500 inline" v-if="in_house"></check-badge-icon>
                        </div>
                        <div class="mt-1 text-sm text-zinc-500">{{ age }} years old · Member since {{  since }}</div>
                    </div>
                </div>

                <SecondaryButton @click="goToBookingsHistory">View training history</SecondaryButton>
            </div>

            <div v-if="isTraining" class="mb-16">
                <training-status :member="member"></training-status>
            </div>

            <div class="space-y-12">
                <user-profile :user="member"></user-profile>
                <user-contact :user="member"></user-contact>
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
    id, role, bookings, name, since, profile_photo_url, in_house, age,
} = props.member

const isTraining = computed(() => bookings.length)

const goToBookingsHistory = () => router.visit(route('admin.members.history', { user: id }))
</script>
