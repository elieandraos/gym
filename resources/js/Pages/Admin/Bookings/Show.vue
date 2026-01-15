<template>
    <AppLayout title="Booking">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="booking.member" :bordered="true"></MemberHeader>
            </PageHeader>

            <p class="mb-8"> {{ description }}</p>

            <BookingSessions :booking-slots="booking.bookingSlots" :trainer="booking.trainer"></BookingSessions>
        </Container>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSessions from '@/Pages/Admin/Bookings/Partials/BookingSessions.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'

const props = defineProps({
    booking: { type: Object, required: true }, // Now includes nested member, trainer, bookingSlots
})

const {
    status, formatted_start_date, formatted_end_date, member, trainer
} = props.booking

const { first_name } = member

const description = computed( () => {
    if (status === 'active') {
        return `Here is ${first_name}'s current training schedule details from ${formatted_start_date} until ${formatted_end_date}`
    } else if (status === 'scheduled') {
        return `Here is ${first_name}'s upcoming training details from ${formatted_start_date} until ${formatted_end_date}`
    } else if (status === 'completed') {
        return `Here is ${first_name}'s training history details from ${formatted_start_date} until ${formatted_end_date}`
    }
})

</script>
