<template>
    <div class="p-4 rounded-lg bg-lime-50 text-zinc-900 flex justify-between items-start">
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-xs text-lime-700">training with</span>
            <div class="text-sm flex">
                <img class="h-6 w-6 mr-1 rounded-full object-cover hidden lg:block" :src="profile_photo_url" :alt="name">
                <span class="text-zinc-700 font-medium">{{ trainer.name }}</span>
            </div>
        </div>
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-xs text-lime-700">upcoming session</span>
            <div class="flex gap-4">
                <div class="text-sm flex items-center gap-1">
                    <calendar-icon class="h-4 w-4 text-lime-600"></calendar-icon>
                    <span class="text-zinc-700 font-medium">{{ upcoming_session_date }}</span>
                    <clock-icon class="ml-4 h-4 w-4 text-lime-600"></clock-icon>
                    <span class="text-zinc-700 font-medium">{{ upcoming_session_time }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-xs text-lime-700">remaining</span>
            <div class="text-sm">
                <span class="tracking-wide text-zinc-700 font-medium">{{ nb_remaining_sessions}}</span>
                <span class="text-lime-700 tracking-wide"> / {{ nb_sessions }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    user: { type: Object, required: true },
})

const { profile_photo_url, name, bookings } = props.user
const {
    nb_sessions, nb_remaining_sessions, trainer, upcoming_session_date, upcoming_session_time
} = bookings[0]
</script>
