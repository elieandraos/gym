<template>
    <AppLayout title="Profile">
        <Container>
            <page-back-button>{{ role }}s</page-back-button>

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

                <div class="space-x-4" v-if="isMember">
                    <SecondaryButton>View training history</SecondaryButton>
                </div>
            </div>

            <div v-if="isMember && isTraining" class="mb-16">
                <member-training-status :user="user"></member-training-status>
            </div>

            <div class="space-y-12">
                <user-profile :user="user"></user-profile>
                <user-contact :user="user"></user-contact>
            </div>

            <div class="mt-12">
                <div v-if="isTrainer && isTraining">
                    <!-- @todo: cards: members this month, sessions this month, most busy days (for ex: Mon,Wed,Th) -->
                    <h3 class="font-semibold text-sm pb-1 mb-1">Members training with {{ name }}</h3>
                    <trainer-bookings :bookings="bookings" :user="user"></trainer-bookings>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MemberTrainingStatus from '@/Pages/Admin/Users/Partials/MemberTrainingStatus.vue'
import TrainerBookings from '@/Pages/Admin/Users/Partials/TrainerBookings.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'
import { CheckBadgeIcon } from '@heroicons/vue/24/solid/index.js'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    user: { type: Object, required: true },
})

const {
    role, bookings, name, since, profile_photo_url, in_house, age
} = props.user

const isMember = computed( () => role === 'Member')
const isTrainer = computed( () => role === 'Trainer')
const isTraining = computed( () => bookings.length)
</script>
