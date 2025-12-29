<template>
    <AppLayout title="Booking">
        <Container>
            <PageHeader :sticky="true">
                <BookingSlotHeader :booking-slot="bookingSlot" :booking-id="bookingId" :with-menu="true"></BookingSlotHeader>
            </PageHeader>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/Components/Layout/PageHeader.vue'

import BookingSlotHeader from '@/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    bookingSlot: { type: Object, required: true },
    bookingId: { type: [Number, String], default: null },
})
</script>
