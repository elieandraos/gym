<template>
    <div v-if="props.isTraining && upcomingSessionDate" class="flex items-center  gap-2">
        <div class="flex-grow">
            <span>
                <Link :href="upcomingSessionUrl" class="text-sky-500 hover:text-sky-700 font-[500]">
                    {{ upcomingSessionDate }}, {{ upcomingSessionTime }}
                </Link>
            </span>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    isTraining: { type: Boolean, required: true },
    activeBooking: { type: Object, default: null },
})

const { upcoming_session_date, upcoming_session_time, upcoming_session_url } = props.activeBooking || {}

const upcomingSessionDate = computed(() => upcoming_session_date)
const upcomingSessionTime = computed(() => upcoming_session_time)
const upcomingSessionUrl = computed(() => upcoming_session_url)
</script>
