<template>
    <AppLayout title="Edit Workout">
        <Container>
            <page-back-button :url="route('admin.bookings-slots.show', bookingSlotWorkout.booking_slot_id)">Back</page-back-button>
            <p class="mt-4">Editing workout coming soon.</p>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    bookingSlotWorkout: { type: Object, required: true },
})
</script>
