<template>
    <table class="min-w-full text-left text-sm/6">
        <thead class="text-zinc-400">
        <tr>
            <th class="border-b border-b-zinc-200 px-4 py-2 font-medium" v-for="header in headers" :key="header">
                {{ header }}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="{ id, date, start_time, end_time, status} in bookingSlots" :key="id">
            <td class="text-zinc-400 p-4">
                {{ date }}
            </td>
            <td class="text-zinc-900 p-4">
                {{ start_time }}
            </td>
            <td class="text-zinc-900 p-4">
                --
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

defineProps({
    bookingSlots: { type: Array, required: true }
})

const headers = ['Date', 'Start Time', 'Trainer', 'Status']

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
