<template>
    <div class="p-4 rounded-lg bg-lime-50 text-zinc-900 flex justify-between items-start">
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-xs text-lime-700">Trainer</span>
            <div class="text-sm flex">
                <img class="h-6 w-6 mr-1 rounded-full object-cover hidden lg:block" :src="profile_photo_url" :alt="name">
                <span class="font-medium">Hanna Nohra</span>
            </div>
        </div>
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-xs text-lime-700">Upcoming</span>
            <div class="flex gap-4">
                <div class="text-sm flex items-center gap-1">
                    <calendar-icon class="h-4 w-4 text-zinc-300"></calendar-icon>
                    <span class="text-zinc-900">May 13</span>
                </div>
                <div class="text-sm flex items-center gap-1">
                    <clock-icon class="h-4 w-4 text-zinc-300"></clock-icon>
                    <span class="text-zinc-900">07:30 am</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-xs text-lime-700">remaining</span>
            <div class="text-sm">
                <span class="font-medium tracking-wide">5 <span class="text-zinc-300">/ 12</span></span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    user: { type: Object, required: true },
})

const { profile_photo_url, name } = props.user
</script>
