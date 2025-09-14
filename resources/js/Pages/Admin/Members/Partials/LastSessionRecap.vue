<template>
    <div class="flex items-start gap-2">
        <DocumentIcon class="size-6 text-[#71717b] flex-shrink-0 hidden lg:block" />
        <div class="flex-grow">
            <p class="text-zinc-950 font-[400]">Recent workouts</p>
            <div v-if="hasCompletedSessions" class="text-sm text-[#71717b]">
                <span v-for="(session, index) in sessionRecap" :key="session.id">
                    <span class="font-[600] text-zinc-950">{{ session.day }}: </span>
                    <span>{{ session.categories }}</span>
                    <span class="text-zinc-950" v-if="index < sessionRecap.length - 1">&nbsp;&nbsp;</span>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import {DocumentIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps({
    activeBooking: { type: Object, default: null },
})

const bookingSlots = props.activeBooking?.bookingSlots || []

const completedSessions = computed(() =>
    bookingSlots
        .filter(slot => slot.status === 'complete')
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(0, 3)
)

const hasCompletedSessions = computed(() => completedSessions.value.length > 0)

const sessionRecap = computed(() =>
    completedSessions.value.map(session => {
        const date = new Date(session.date)
        const day = date.toLocaleDateString('en-US', { weekday: 'short' })

        const categories = session.workouts
            ?.map(workout => workout.category)
            .filter((category, index, arr) => arr.indexOf(category) === index)
            .sort()
            .join(',') || ''

        return {
            id: session.id,
            day,
            categories
        }
    })
)
</script>
