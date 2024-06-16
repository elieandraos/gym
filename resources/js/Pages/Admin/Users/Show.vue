<template>
    <AppLayout title="Profile">
        <Container>
            <page-back-button>{{ role }}s</page-back-button>
            <div class="lg:my-12 lg:mx-auto" v-if="role === 'Member'">
                <member-training-status :user="user"></member-training-status>
            </div>
            <div class="space-y-12 lg:space-y-0 lg:flex lg:gap-24 lg:justify-between">
                <user-profile :user="user" class="lg:w-[45%]"></user-profile>
                <user-contact :user="user" class="lg:w-[45%]"></user-contact>
            </div>
            <div class="mt-12">
                <div v-if="role === 'Member' && bookings.length">
                    <div class="font-medium text-sm/relaxed capitalize">Training schedule</div>
                    <booking-sessions :booking-slots="bookings[0].bookingSlots"></booking-sessions>
                </div>
                <div v-if="role === 'Trainer' && bookings.length">
                    <trainer-bookings :bookings="bookings"  :user="user" v-if="role === 'Trainer'"></trainer-bookings>
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

const props = defineProps({
    user: { type: Object, required: true },
})

const {
    role, bookings,
} = props.user
</script>
