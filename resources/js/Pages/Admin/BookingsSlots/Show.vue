<template>
    <AppLayout title="Booking">
        <Container>
            <page-back-button>Back</page-back-button>

            <div class="flex justify-between items-center pb-6 mb-12 border-b border-zinc-200">
                <div class="grow">
                    <div class="flex gap-x-4">
                        <h1 class="text-xl font-bold text-zinc-950">Training Session Details</h1>
                        <Badge :type="badge_type">{{ status }}</Badge>
                    </div>
                    <div class="flex gap-x-12 mt-3 text-sm text-zinc-500">
                        <div class="flex gap-x-2">
                            <UsersIcon class="w-4 text-zinc-500"></UsersIcon>
                            <span>{{ booking.member.name}} · {{ booking.trainer.name }}</span>
                        </div>
                        <div class="flex gap-x-2">
                            <ClockIcon class="w-4 text-zinc-500"></ClockIcon>
                            <span>{{ formatted_date }} · {{ start_time }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-x-4">
                    <SecondaryButton>Change date</SecondaryButton>
                    <SecondaryButton>Mark as cancelled</SecondaryButton>
                    <PrimaryButton>Add workout</PrimaryButton>
                </div>
            </div>

            Workout details to follow (curren workout add/edit/remove and last 3 workout details)...
        </Container>
    </AppLayout>
</template>

<script setup>

import Badge from '@/Components/Layout/Badge.vue'
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { UsersIcon, ClockIcon }  from '@heroicons/vue/24/solid/index.js'

const props = defineProps({
    bookingSlot: { type: Object, required: true }
})

const { booking, formatted_date , start_time, status, badge_type} = props.bookingSlot
</script>
