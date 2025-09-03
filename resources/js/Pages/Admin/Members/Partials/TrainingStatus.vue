<template>
    <div>
        <div class="flex flex-col gap-2 sm:flex-row md:justify-between md:gap-x-4">
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-6 font-bold text-zinc-950 capitalize">Training with</div>
                <div class="mt-2 text-sm flex gap-2 items-center">
                    <img class="h-6 w-6 rounded-full object-cover" :src="trainer.profile_photo_url" :alt="name">
                    <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-sky-500 hover:text-sky-700 font-medium">{{ trainer.name }}</Link>
                </div>
            </div>
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-6 font-bold text-zinc-950 capitalize">upcoming session</div>
                <div class="mt-3 text-sm flex gap-2 items-center">
                    <Link :href="upcoming_session_url" class="flex items-center gap-1 font-medium text-sm" >
                        <calendar-icon class="h-4 w-4 text-zinc-400"></calendar-icon>
                        <span class="text-sky-500 hover:text-sky-700">{{ upcoming_session_date }}</span>
                        <clock-icon class="ml-4 h-4 w-4 text-zinc-400"></clock-icon>
                        <span class="text-sky-500 hover:text-sky-700">{{ upcoming_session_time }}</span>
                    </Link>
                </div>
            </div>
            <div class="grow">
                <hr class="w-full border-t border-zinc-200">
                <div class="mt-6 font-bold text-zinc-950 capitalize">Remaining</div>
                <div class="mt-3 text-sm">
                    {{ nb_remaining_sessions }} until {{ formatted_end_date }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { name, active_booking } = props.member

const {
    nb_remaining_sessions, trainer, upcoming_session_date, upcoming_session_time, formatted_end_date, upcoming_session_url,
} = active_booking
</script>
