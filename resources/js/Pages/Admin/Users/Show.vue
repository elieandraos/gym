<template>
    <AppLayout title="Profile">
        <Container>
            <page-back-button>{{ role }}s</page-back-button>
<!--            <div class="lg:my-12 lg:mx-auto" v-if="isMember && isTraining">-->
<!--                <member-training-status :user="user"></member-training-status>-->
<!--            </div>-->
            <div class="space-y-12 lg:space-y-0 lg:flex lg:flex-row lg:gap-24 lg:justify-between">
                <user-profile :user="user" class="lg:w-[45%]"></user-profile>
                <user-contact :user="user" class="lg:w-[45%]"></user-contact>
            </div>
            <div class="mt-12">
                <div v-if="isMember && isTraining">
                    <div class="font-medium text-sm/relaxed capitalize mb-4">Training schedule</div>
                    <booking-sessions :booking-slots="bookings[0].bookingSlots" :trainer="bookings[0].trainer"></booking-sessions>
                </div>
                <div v-if="isTrainer && isTraining">
                    <div class="font-medium text-sm/relaxed capitalize mb-4">Trainees list</div>
                    <trainer-bookings :bookings="bookings" :user="user"></trainer-bookings>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSessions from '@/Pages/Admin/Bookings/Partials/BookingSessions.vue'
import MemberTrainingStatus from '@/Pages/Admin/Users/Partials/MemberTrainingStatus.vue'
import TrainerBookings from '@/Pages/Admin/Users/Partials/TrainerBookings.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

import { computed } from 'vue'

const props = defineProps({
    user: { type: Object, required: true },
})

const {
    role, bookings,
} = props.user

const isMember = computed( () => role === 'Member')
const isTrainer = computed( () => role === 'Trainer')
const isTraining = computed( () => bookings.length)
</script>
