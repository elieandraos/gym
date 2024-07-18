<template>
    <AppLayout title="Booking">
        <Container>
            <page-back-button>Back</page-back-button>

            <div>
                <div class="mt-8 flex flex-col gap-8 sm:flex-row md:justify-between md:gap-16">
                    <div class="grow">
                        <hr class="w-full border-t border-zinc-200">
                        <div class="mt-3 text-sm font-medium text-zinc-400">Trainer</div>
                        <div class="mt-2 text-lg font-medium flex gap-2 items-center">
                            <img class="h-8 w-8 rounded-full object-cover" :src="trainer.profile_photo_url" :alt="name">
                            <span>{{ trainer.name }}</span>
                        </div>
                    </div>
                    <div class="grow">
                        <hr class="w-full border-t border-zinc-200">
                        <div class="mt-3 text-sm font-medium text-zinc-400">Member</div>
                        <div class="mt-2 text-lg font-medium flex gap-2 items-center">
                            <img class="h-8 w-8 rounded-full object-cover" :src="member.profile_photo_url" :alt="name">
                            <span>{{ member.name }}</span>
                        </div>
                    </div>
                    <div class="grow">
                        <hr class="w-full border-t border-zinc-200">
                        <div class="mt-3 text-sm font-medium text-zinc-400">Sessions Remaining</div>
                        <div class="mt-2 text-lg font-medium">
                            {{ nb_remaining_sessions }} / {{ nb_sessions }}
                        </div>
                    </div>
                </div>

                <h3 class="font-medium mt-16 mb-4">sessions information</h3>
                <BookingSessions :booking-slots="bookingSlots" :trainer="trainer"></BookingSessions>

            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSessions from '@/Pages/Admin/Bookings/Partials/BookingSessions.vue'

const props = defineProps({
    booking: { type: Object, required: true },
})

const { member, trainer, nb_sessions, nb_remaining_sessions, bookingSlots} = props.booking
</script>
