<template>
    <ul class="lg:hidden">
        <li
            v-for="{ id, formatted_date, start_time, status} in bookingSlots"
            :key="id"
            class="py-4 pl-1 pr-2 flex rounded-lg hover:bg-stone-100"
        >
            <div class="flex items-center justify-between gap-2 w-full">
                <img :src="trainer.profile_photo_url"  :alt="trainer.name" class="rounded-full w-10"/>
                <div>
                    <h4 class="font-medium text-sm">{{ trainer.name }}</h4>
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

    <table class="hidden lg:table min-w-full text-left text-sm/6">
        <thead class="text-zinc-400">
            <tr>
                <th class="border-b border-b-zinc-200 px-4 py-2 font-medium" v-for="header in headers" :key="header">
                    {{ header }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="{ id, formatted_date, start_time, status} in bookingSlots" :key="id">
                <td class="text-zinc-900 p-4 flex items-center gap-2 font-medium">
                    <img class="w-8 h-8 rounded-full" :src="trainer.profile_photo_url"  alt=""/>
                    <span>{{ trainer.name }}</span>
                </td>
                <td class="text-zinc-400 p-4">
                    {{ formatted_date }}
                </td>
                <td class="text-zinc-900 p-4">
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
import {CalendarIcon, ClockIcon} from '@heroicons/vue/24/outline/index.js'

defineProps({
    bookingSlots: { type: Array, required: true },
    trainer: { type: Object, required: true },
})

const headers = ['Trainer', 'Date', 'Time', 'Status']

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
