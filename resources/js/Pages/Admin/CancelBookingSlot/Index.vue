<template>
    <AppLayout title="Cancel Session">
        <Container>
            <session-header :booking-slot="bookingSlot"></session-header>

            <div class="text-zinc-500 my-8">Are you sure you want to cancel this session?</div>

            <div class="flex justify-end mt-8 gap-4">
                <Link :href="route('admin.bookings-slots.show', id)">
                    <TransparentButton>No, I have changed my mind</TransparentButton>
                </Link>
                <primary-button @click="cancelBookingSlot">Affirmative!</primary-button>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import SessionHeader from '@/Pages/Admin/BookingsSlots/Partials/SessionHeader.vue'
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
    router.delete(route('admin.cancel-booking-slot.destroy', id))
}
</script>

