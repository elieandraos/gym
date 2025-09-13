<template>
    <AppLayout title="Cancel Session">
        <Container>
            <PageHeader :sticky="true">
                <BookingSlotHeader :booking-slot="bookingSlot"></BookingSlotHeader>
            </PageHeader>

            <div class="space-y-8">
                <div class="">Are you sure you want to cancel this session?</div>

                <div class="flex gap-4">
                    <primary-button @click="cancelBookingSlot">Affirmative!</primary-button>

                    <Link :href="route('admin.bookings-slots.show', id)">
                        <TransparentButton>No</TransparentButton>
                    </Link>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/Components/Layout/PageHeader.vue'
import BookingSlotHeader from '@/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
})

const { id } = props.bookingSlot
const { route } = window

const cancelBookingSlot = () => {
    router.delete(route('admin.bookings-slots.cancel.destroy', id))
}
</script>
