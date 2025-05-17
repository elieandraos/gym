<template>
    <div>
        <div class="flex flex-col gap-2 sm:flex-row md:justify-between md:gap-x-4">
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-6 font-semibold text-zinc-950 capitalize">Training with</div>
                <div class="mt-3 text-sm flex gap-2 items-center">
                    <img class="h-6 w-6 rounded-full object-cover" :src="trainer.profile_photo_url" :alt="name">
                    <span class="text-zinc-950">{{ trainer.name }}</span>
                </div>
            </div>
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-6 font-semibold text-zinc-950 capitalize">upcoming session</div>
                <div class="mt-3 text-sm flex gap-2 items-center">
                    <div class="flex items-center gap-1">
                        <calendar-icon class="h-4 w-4 text-zinc-400"></calendar-icon>
                        <span class="text-zinc-700">{{ upcoming_session_date }}</span>
                        <clock-icon class="ml-4 h-4 w-4 text-zinc-400"></clock-icon>
                        <span class="text-zinc-700">{{ upcoming_session_time }}</span>
                    </div>
                </div>
                <div class="mt-2">
                    <Link :href="upcoming_session_url" class="text-sky-500 hover:text-sky-700 font-medium text-sm" >
                        view session details
                    </Link>
                </div>
            </div>
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-6 font-semibold text-zinc-950 capitalize">Remaining</div>
                <div class="mt-3 text-sm">
                    {{ nb_remaining_sessions }} until {{ formatted_end_date }}
                </div>
                <div class="mt-2">
                    <Link :href="route('admin.bookings.show', id)" class="text-sky-500 hover:text-sky-700 font-medium text-sm" >
                        view all sessions
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    member: { type: Object, required: true },
})

const { route } = window

const { name, bookings } = props.member

const {
    id, nb_remaining_sessions, trainer, upcoming_session_date, upcoming_session_time, formatted_end_date, upcoming_session_url,
} = bookings[0]
</script>
