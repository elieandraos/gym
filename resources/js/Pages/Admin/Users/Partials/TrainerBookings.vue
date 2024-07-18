<template>
    <div class="hidden lg:block">
        <table class="min-w-full text-left text-sm/6">
            <thead class="text-zinc-400">
                <tr>
                    <th class="border-b border-b-zinc-200 px-4 py-2 font-medium" v-for="header in headers" :key="header">
                        {{ header }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="{ id, member, upcoming_session_date, upcoming_session_time, nb_remaining_sessions } in bookings" :key="id">
                    <td class="text-zinc-900 font-medium p-4 flex gap-2 items-center">
                        <img class="w-8 h-8 rounded-full" :src="member.profile_photo_url"  alt=""/>
                        {{ member.name }}
                    </td>
                    <td class="text-zinc-400 p-4">
                        {{ upcoming_session_date }}
                    </td>
                    <td class="text-zinc-900 p-4">
                        {{ upcoming_session_time }}
                    </td>
                    <td class="text-zinc-900 p-4">
                        {{ nb_remaining_sessions }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="lg:hidden">
        <h3 class="font-medium pb-2 mb-2 border-b border-b-zinc-200 -mt-1">
            <span>{{ bookings.length }}</span>
            members training with {{ first_name }}
        </h3>
        <ul>
            <li
                v-for="{ id, member, upcoming_session_date, upcoming_session_time, nb_remaining_sessions } in bookings"
                :key="id"
                class="py-4 pl-1 pr-2 flex rounded-lg hover:bg-stone-100 cursor-pointer"
                @click="goToBooking(id)"
            >
                <div class="flex items-center justify-between gap-2 w-full">
                    <img :src="member.profile_photo_url"  :alt="member.name" class="rounded-full w-10"/>
                    <div>
                        <h4 class="font-medium text-sm">{{ member.name }}</h4>
                        <div class="text-xs/6 text-zinc-400 flex gap-1">
                            <CalendarIcon class="w-4 text-zinc-400"></CalendarIcon>
                            <span class="font-medium">{{ upcoming_session_date }}</span>
                            <ClockIcon class="ml-4 w-4 text-zinc-400"></ClockIcon>
                            <span class="font-medium">{{ upcoming_session_time }}</span>
                        </div>
                    </div>
                    <div class="grow flex justify-end">
                        <Badge type="success">{{ nb_remaining_sessions }} remaining</Badge>
                    </div>
                </div>

            </li>
        </ul>
    </div>
</template>

<script setup>
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'

import Badge from '@/Components/Layout/Badge.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    user: { type: Object, required: true },
    bookings: { type: Array, required: true },
})

const { route } = window
const { first_name } = props.user

const headers = ['Member', 'Date', 'Time', '# Remaining']

const goToBooking = (id) => router.visit( route('admin.bookings.show', id))
</script>
