<template>
    <AppLayout title="Booking">
        <Container>
            <PageHeader :sticky="true">
                <BookingSlotHeader :booking-slot="bookingSlot" :booking="booking" :booking-id="bookingId" :with-menu="true"></BookingSlotHeader>
            </PageHeader>

            <!-- Trello-style Circuit Board -->
            <div class="flex flex-col md:flex-row gap-4 md:overflow-x-auto pb-4 px-4 mt-6">
                <!-- Add Circuit Button - Shows first on mobile, last on desktop -->
                <div class="md:hidden">
                    <AddCircuitButton :booking-slot-id="bookingSlot.id" />
                </div>

                <!-- Circuit Columns -->
                <CircuitColumn
                    v-for="circuit in circuits"
                    :key="circuit.id"
                    :circuit="circuit"
                    :booking-slot-id="bookingSlot.id"
                    :available-workouts="availableWorkouts"
                />

                <!-- Add Circuit Button - Desktop only -->
                <div class="hidden md:block">
                    <AddCircuitButton :booking-slot-id="bookingSlot.id" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'

import BookingSlotHeader from '@/Pages/Admin/BookingsSlots/Partials/BookingSlotHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CircuitColumn from '@/Pages/Admin/BookingsSlots/Partials/CircuitColumn.vue'
import AddCircuitButton from '@/Pages/Admin/BookingsSlots/Partials/AddCircuitButton.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    booking: { type: Object, required: true },
    bookingId: { type: [Number, String], default: null },
    workouts: { type: Array, default: () => [] },
})

// Use computed to reactively get data from props
const availableWorkouts = computed(() => props.workouts)

// Use computed to reactively get circuits from booking slot
const circuits = computed(() => props.bookingSlot.circuits || [])
</script>
