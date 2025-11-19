<template>
    <div class="flex justify-between items-center font-normal gap-4">
        <PageBackButton />

        <div class="grow">
            <div class="flex gap-x-4 items-center">
                <h1 class="text-[20px] font-[600] leading-[32px]">Session Details</h1>
                <Badge class="text-xs" :type="badge_type">{{ status }}</Badge>
            </div>
            <div class="flex flex-col md:flex-row md:gap-x-12 gap-y-1 mt-1 text-[#71717b]">
                <div class="flex gap-x-2">
                    <UsersIcon class="w-4 text-zinc-500 flex-shrink-0"></UsersIcon>
                    <span class="text-[15px]">
                        <Link class="text-sky-500 hover:text-sky-700 font-[400]" :href="route('admin.members.show', { user: booking.member.id })"> {{ booking.member.name}}</Link>
                        ·
                        <Link class="text-sky-500 hover:text-sky-700 font-[400]" :href="route('admin.trainers.show', { user: booking.trainer.id })"> {{ booking.trainer.name}}</Link>
                    </span>
                </div>
                <div class="flex gap-x-2">
                    <ClockIcon class="w-4 text-[#71717b] flex-shrink-0"></ClockIcon>
                    <span class="text-[15px]">{{ formatted_date }} · {{ start_time }}</span>
                </div>
            </div>
        </div>

        <div v-if="withMenu" class="flex items-center gap-2">
            <Link v-if="bookingId" :href="route('admin.bookings.show', bookingId)" class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer" title="View booking schedule">
                <CalendarDaysIcon class="w-5 h-5 text-zinc-600" />
            </Link>
            <dropdown direction="left">
                <div class="space-y-2">
                    <a :href="route('admin.bookings-slots.workout.create', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Add workouts</a>
                    <Link :href="route('admin.change-booking-slot-date-time.edit', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Change date & time</Link>
                    <hr class="border-gray-200">
                    <Link :href="route('admin.members.show', { user: booking.member.id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">{{ booking.member.name.split(' ')[0] }}'s profile</Link>
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
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import { CalendarDaysIcon, ClockIcon, UsersIcon } from '@heroicons/vue/24/solid'

import { Link } from '@inertiajs/vue3'
import { toRefs } from 'vue'

const { route } = window

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    bookingId: { type: [Number, String], default: null },
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
