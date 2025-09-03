<template>
    <AppLayout title="Booking">
        <Container>
            <page-title :sticky="true">
                <member-header :member="member" :bordered="true"></member-header>
            </page-title>

            <BookingSessions :booking-slots="bookingSlots" :trainer="trainer"></BookingSessions>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSessions from '@/Pages/Admin/Bookings/Partials/BookingSessions.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'

const props = defineProps({
    booking: { type: Object, required: true },
})

const {
    member, trainer, bookingSlots,
} = props.booking
</script>
