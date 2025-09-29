<template>
    <AppLayout title="Daily Calendar">
        <Container>
            <h1 class="text-2xl font-bold mb-6">Daily Calendar</h1>

            <!-- Trainer Filter -->
            <div class="mb-6 flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Filter by trainers:</span>
                <template v-for="trainer in available_trainers" :key="trainer.id">
                    <label class="inline-flex items-center text-sm cursor-pointer">
                        <input
                            type="checkbox"
                            v-model="selectedTrainers"
                            :value="trainer.id"
                            @change="applyTrainerFilter"
                            class="h-4 w-4 rounded border-gray-300 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="ml-2 text-gray-700 cursor-pointer">{{ trainer.first_name }}</span>
                    </label>
                </template>
            </div>

            <!-- Date Navigation -->
            <div class="mb-6 flex items-center justify-between">
                <button
                    @click="prevDay"
                    class="text-gray-500 hover:text-gray-700 cursor-pointer px-3 py-1 border rounded"
                >
                    Previous Day
                </button>
                <h2 class="text-lg font-semibold">
                    {{ formattedDate }}
                    <span v-if="is_today" class="text-sm text-green-600">(Today)</span>
                </h2>
                <button
                    @click="nextDay"
                    class="text-gray-500 hover:text-gray-700 cursor-pointer px-3 py-1 border rounded"
                >
                    Next Day
                </button>
            </div>

            <!-- Events List -->
            <div class="space-y-4">
                <div v-if="events.length === 0" class="text-gray-500 text-center py-8">
                    No sessions scheduled for this day.
                </div>
                <div
                    v-for="event in events"
                    :key="event.id"
                    class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ event.title }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ formatTime(event.start_time) }} - {{ formatTime(event.end_time) }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Trainer: {{ event.meta_data.trainer }}
                            </p>
                        </div>
                        <a
                            :href="event.url"
                            class="px-4 py-2 bg-black text-white text-sm rounded hover:bg-gray-800"
                        >
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { format, parseISO, addDays } from 'date-fns'

import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    events: { type: Array, required: true },
    is_today: { type: Boolean, required: true },
    available_trainers: { type: Array, required: true },
    filters: { type: Object, required: true }
})

const { route } = window
const selectedTrainers = ref(props.filters?.trainers || [])

// Date formatting
const formattedDate = computed(() => {
    if (!props.filters?.date) return ''
    return format(parseISO(props.filters.date), 'EEEE, MMMM d, yyyy')
})

// Time formatting
const formatTime = (timeString) => {
    return format(parseISO(timeString), 'h:mm a')
}

// Navigation
const getNavParams = (date) => {
    const params = { date: format(date, 'yyyy-MM-dd') }

    if (selectedTrainers.value.length > 0) {
        params.trainers = selectedTrainers.value.join(',')
    }

    return params
}

const prevDay = () => {
    if (!props.filters?.date) return
    const currentDate = parseISO(props.filters.date)
    const newDate = addDays(currentDate, -1)

    router.get(route('admin.daily-calendar.index'), getNavParams(newDate))
}

const nextDay = () => {
    if (!props.filters?.date) return
    const currentDate = parseISO(props.filters.date)
    const newDate = addDays(currentDate, 1)

    router.get(route('admin.daily-calendar.index'), getNavParams(newDate))
}

// Trainer filtering
const applyTrainerFilter = () => {
    if (!props.filters?.date) return
    const currentDate = parseISO(props.filters.date)

    router.get(route('admin.daily-calendar.index'), getNavParams(currentDate), {
        preserveState: true,
        preserveScroll: true
    })
}
</script>