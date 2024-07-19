<template>
    <ul class="lg:hidden">
        <li
            v-for="{ id, formatted_date, start_time, status} in bookingSlots"
            :key="id"
            class="py-4 pl-1 pr-2 flex rounded-lg hover:bg-zinc-100 cursor-pointer"
            @click="goToBookingSlot(id)"
        >
            <div class="flex items-center justify-between gap-2 w-full">
                <img :src="profile_photo_url"  :alt="name" class="rounded-full w-10"/>
                <div>
                    <h4 class="font-medium text-sm">{{ name }}</h4>
                    <div class="text-xs/6 text-zinc-400 flex gap-1">
                        <CalendarIcon class="w-4 text-zinc-400"></CalendarIcon>
                        <span class="font-medium">{{ formatted_date }}</span>
                        <ClockIcon class="ml-4 w-4 text-zinc-400"></ClockIcon>
                        <span class="font-medium">{{ start_time }}</span>
                    </div>
                </div>
                <div class="grow flex justify-end">
                    <Badge :type="statusBadgeType(status)">{{ status }}</Badge>
                </div>
            </div>

        </li>
    </ul>

    <table class="min-w-full text-left lg:table text-sm">
        <thead class="text-zinc-400">
            <tr>
                <th class="border-b border-b-zinc-200 px-4 py-2 text-sm font-medium" v-for="header in headers" :key="header">
                    {{ header }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="{ id, formatted_date, start_time, status} in bookingSlots"
                :key="id"
                class="border-b border-zinc-100 hover:bg-zinc-100 hover:cursor-pointer"
                @click="goToBookingSlot(id)"
            >
                <td class="text-zinc-400 p-4">
                    {{ formatted_date }}
                </td>
                <td class="text-zinc-900 p-4 font-medium">
                    {{ start_time }}
                </td>
                <td class="text-zinc-900 p-4">
                    <Badge :type="statusBadgeType(status)">{{ status }}</Badge>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import Badge from '@/Components/Layout/Badge.vue'
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline/index.js'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    bookingSlots: { type: Array, required: true },
    trainer: { type: Object, required: true },
})

const { name, profile_photo_url } = props.trainer

const headers = [ 'Date', 'Time', 'Status']

const goToBookingSlot = (id) => router.visit(route('admin.bookings-slots.show', { bookingSlot: id }))

const statusBadgeType = (status) => {
    let type

    switch(status) {
        case 'upcoming':
            type = 'warning'
            break
        case 'cancelled':
            type = 'error'
            break
        case 'frozen':
            type = 'info'
            break
        case 'complete': default:
            type = 'success'
            break
    }

    return type
}
</script>
