<template>
    <AppLayout title="Profile">
        <Container>
            <page-back-button>Trainers</page-back-button>

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
            </div>

            <div class="space-y-12">
                <user-profile :user="trainer"></user-profile>
                <user-contact :user="trainer"></user-contact>
            </div>

            <div class="mt-12">
                <div v-if="isTraining">
                    <!-- @todo: cards: members this month, sessions this month, most busy days (for ex: Mon,Wed,Th) -->
                    <h3 class="font-semibold text-sm pb-1 mb-1">Members training with {{ name }}</h3>
                    <trainer-bookings :bookings="bookings" :trainer="trainer"></trainer-bookings>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { CheckBadgeIcon } from '@heroicons/vue/24/solid'
import { computed } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import TrainerBookings from '@/Pages/Admin/Trainers/Partials/TrainerBookings.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const props = defineProps({
    trainer: { type: Object, required: true },
})

const {
    id, role, bookings, name, since, profile_photo_url, in_house, age,
} = props.trainer

const isTraining = computed(() => bookings.length)
</script>
