<template>
    <div class="flex justify-between items-end font-normal">
        <div class="grow">
            <div class="flex gap-x-4">
                <h1 class="text-xl font-bold text-zinc-950">Session Details</h1>
                <Badge :type="badge_type">{{ status }}</Badge>
            </div>
            <div class="flex gap-x-12 mt-3 text-sm text-zinc-500">
                <div class="flex gap-x-2">
                    <UsersIcon class="w-4 text-zinc-500"></UsersIcon>
                    <span>
                        <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.members.show', { user: booking.member.id })"> {{ booking.member.name}}</Link>
                        ·
                        <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.trainers.show', { user: booking.trainer.id })"> {{ booking.trainer.name}}</Link>
                    </span>
                </div>
                <div class="flex gap-x-2">
                    <ClockIcon class="w-4 text-zinc-500"></ClockIcon>
                    <span>{{ formatted_date }} · {{ start_time }}</span>
                </div>
            </div>
        </div>

        <div v-if="withMenu" class="space-x-4">
            <dropdown direction="right">
                <div class="space-y-2">
                    <a :href="route('admin.bookings-slots.workout.create', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Add workouts</a>
                    <Link :href="route('admin.change-booking-slot-date-time.edit', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Change date & time</Link>
                    <hr class="border-gray-200">
                    <a :href="route('admin.bookings-slots.cancel.index', id)" class="block p-2 text-red-500 hover:bg-red-50 hover:rounded-lg">Cancel session</a>
                </div>
            </dropdown>
        </div>
    </div>
</template>

<script setup>
import Badge from '@/Components/Layout/Badge.vue'
import Dropdown from '@/Components/Layout/Dropdown.vue'
import { ClockIcon, UsersIcon } from '@heroicons/vue/24/solid'

import { Link } from '@inertiajs/vue3'
import { toRefs } from 'vue'

const { route } = window

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    withMenu: { type: Boolean, default: false },
})

const {
    id,
    booking,
    formatted_date,
    start_time,
    status,
    badge_type,
} = toRefs(props.bookingSlot)
</script>
