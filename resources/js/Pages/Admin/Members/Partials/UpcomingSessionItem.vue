<template>
    <div v-if="isTraining && upcomingSessionDate" class="flex items-center gap-3">
        <ClockIcon class="size-6 text-gray-500 flex-shrink-0" />
        <div class="flex-grow">
            <span>
                Upcoming session on
                <Link :href="upcomingSessionUrl" class="text-blue-600 hover:text-blue-800 font-medium">
                    {{ upcomingSessionDate }} {{ upcomingSessionTime }}
                </Link>
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

const { active_booking: activeBooking } = props.member
const isTraining = computed(() => !!activeBooking)
const upcomingSessionDate = computed(() => activeBooking?.upcoming_session_date)
const upcomingSessionTime = computed(() => activeBooking?.upcoming_session_time)
const upcomingSessionUrl = computed(() => activeBooking?.upcoming_session_url)
</script>
