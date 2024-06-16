<template>
    <div>
        <div class="mt-8 flex flex-col gap-8 sm:flex-row md:justify-between md:gap-16">
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-3 text-sm font-medium text-zinc-400 capitalize">Trainer</div>
                <div class="mt-2 text-lg font-medium flex gap-2 items-center">
                    <img class="h-8 w-8 rounded-full object-cover" :src="trainer.profile_photo_url" :alt="name">
                    <span>{{ trainer.name }}</span>
                </div>
            </div>
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-3 text-sm font-medium text-zinc-400 capitalize">upcoming session</div>
                <div class="mt-2 text-lg font-medium flex gap-2 items-center">
                    <div class="text-base flex items-center gap-1">
                        <calendar-icon class="h-4 w-4 text-zinc-400"></calendar-icon>
                        <span class="text-zinc-700 font-medium ">{{ upcoming_session_date }}</span>
                        <clock-icon class="ml-4 h-4 w-4 text-zinc-400"></clock-icon>
                        <span class="text-zinc-700 font-medium">{{ upcoming_session_time }}</span>
                    </div>
                </div>
            </div>
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-3 text-sm font-medium text-zinc-400 capitalize">Sessions Remaining</div>
                <div class="mt-2 text-lg font-medium">
                    {{ nb_remaining_sessions }} <span class="text-xs text-zinc-500">/ {{ nb_sessions }}</span>
                </div>
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
