<template>
    <AppLayout title="Cancel Session">
        <Container>
            <booking-slot-header :booking-slot="bookingSlot"></booking-slot-header>

            <div class="flex justify-start items-center gap-4">
                <div class="text-zinc-500">Are you sure you want to cancel this session?</div>

                <div class="flex justify-end gap-4">
                    <primary-button @click="cancelBookingSlot">Affirmative!</primary-button>

                    <Link :href="route('admin.bookings-slots.show', id)">
                        <TransparentButton>No, I have changed my mind</TransparentButton>
                    </Link>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
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
