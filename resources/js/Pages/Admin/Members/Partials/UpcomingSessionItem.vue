<template>
    <div v-if="isTraining && upcomingSessionDate" class="flex items-center gap-3">
        <ClockIcon class="size-6 text-[#71717b] flex-shrink-0" />
        <div class="flex-grow">
            <span>
                <Link :href="upcomingSessionUrl" class="text-sky-500 hover:text-sky-700 font-[500]">
                    Upcoming session
                </Link>
                on <span class="font-[600] text-zinc-950">{{ upcomingSessionDate }}, {{ upcomingSessionTime }}</span>
            </span>
        </div>
    </div>
</template>

<script setup>
import { ClockIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking } = props.member || {}
const { upcoming_session_date, upcoming_session_time, upcoming_session_url } = activeBooking || {}

const isTraining = computed(() => !!activeBooking)
const upcomingSessionDate = computed(() => upcoming_session_date)
const upcomingSessionTime = computed(() => upcoming_session_time)
const upcomingSessionUrl = computed(() => upcoming_session_url)
</script>
