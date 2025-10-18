<template>
    <div class="flex items-center gap-2">
        <div class="flex-grow">
            <div v-if="hasCompletedSessions" class="text-zinc-950">
                <span v-for="(session, index) in sessionRecap" :key="session.id">
                    <Link :href="route('admin.bookings-slots.show', session.id)" class="font-[600] text-sky-500 hover:text-sky-700">{{ session.day }}</Link><span class="font-[600]">: </span>
                    <span>{{ session.categories }}</span>
                    <span class="text-zinc-950" v-if="index < sessionRecap.length - 1">&nbsp;&nbsp;</span>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    activeBooking: { type: Object, default: null },
})

const { route } = window

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
