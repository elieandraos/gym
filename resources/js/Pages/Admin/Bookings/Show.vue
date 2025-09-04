<template>
    <AppLayout title="Booking">
        <Container>
            <page-title :sticky="true">
                <member-header :member="member" :bordered="true"></member-header>
            </page-title>

            <p class="mb-8"> {{ description }}</p>

            <BookingSessions :booking-slots="bookingSlots" :trainer="trainer"></BookingSessions>
        </Container>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSessions from '@/Pages/Admin/Bookings/Partials/BookingSessions.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'

const props = defineProps({
    booking: { type: Object, required: true },
})

const {
    member, trainer, bookingSlots, status, formatted_start_date, formatted_end_date
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
