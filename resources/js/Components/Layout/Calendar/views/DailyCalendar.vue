<template>
    <div>
        <!-- Calendar Header with navigation and filters -->
        <div class="sticky top-0 z-50 bg-white px-8 py-3 border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between max-w-7xl">
                <div class="flex items-center space-x-2">
                    <button
                        @click="prevDay"
                        class="text-gray-500 hover:text-gray-700 cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        @click="nextDay"
                        class="text-gray-500 hover:text-gray-700 cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <span class="text-gray-900 text-lg font-medium ml-4">
                        {{ dateLabel }}
                    </span>
                </div>

                <TrainerFilter
                    v-model="selectedTrainers"
                    :available-trainers="availableTrainers"
                    @filter-change="handleFilterChange"
                    @update:modelValue="updateTrainerSelection"
                />
            </div>
        </div>

        <!-- Sticky Days Header -->
        <div class="sticky top-[4.5rem] z-30 bg-white shadow-sm border-b border-gray-200">
            <div class="flex">
                <div class="flex-none w-14 bg-white"></div>
                <div class="flex-auto">
                    <div class="flex h-14 items-center justify-center">
                        <h3 class="text-sm font-semibold text-gray-900">{{ dateLabel }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Grid -->
        <DailyCalendarGrid
            :hours="hours"
            :filtered-events="filteredEvents"
            @open-modal="openMembersPopup"
        />

        <!-- Members Modal -->
        <EventModal
            :is-open="showMembersPopup"
            :selected-slot="selectedSlot"
            @close="closeMembersPopup"
            @go-to-member="goToBookingSlot"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { parseISO, format } from 'date-fns'

// Components
import CalendarHeader from '../components/CalendarHeader.vue'
import TrainerFilter from '../components/TrainerFilter.vue'
import DailyCalendarGrid from './DailyCalendarGrid.vue'
import EventModal from '../components/EventModal.vue'

// Composables
import { useDailyCalendarNavigation } from '../composables/useDailyCalendarNavigation.js'
import { useTrainerFiltering } from '../composables/useTrainerFiltering.js'
import { useDailyCalendarEvents } from '../composables/useDailyCalendarEvents.js'
import { useEventPositioning } from '../composables/useEventPositioning.js'
import { useEventModal } from '../composables/useEventModal.js'

const props = defineProps({
    events: Array,
    is_today: Boolean,
    available_trainers: Array,
    filters: { type: Object, default: () => ({}) },
    startHour: { type: Number, default: 6 },
    endHour: { type: Number, default: 22 }
})

const { route } = window

// Set up trainer filtering state first
const { selectedTrainers } = useTrainerFiltering(
    props.filters,
    null, // getNavParams not needed for state setup
    'admin.daily-calendar.index'
)

// Set up navigation with trainer filtering
const { prevDay, nextDay } = useDailyCalendarNavigation(
    props.filters,
    selectedTrainers,
    'admin.daily-calendar.index'
)

// Set up calendar events
const { dateLabel, hours, rawMerged } = useDailyCalendarEvents(
    props.events,
    props.filters,
    props.startHour,
    props.endHour
)

// Set up event positioning
const { filteredEvents } = useEventPositioning(rawMerged)

// Set up modal
const { showMembersPopup, selectedSlot, openMembersPopup, closeMembersPopup, goToBookingSlot } = useEventModal(props.events)

// Computed properties
const availableTrainers = computed(() => props.available_trainers || [])

// Update trainer selection and navigate immediately
const updateTrainerSelection = (newSelection) => {
    selectedTrainers.value = newSelection

    if (props.filters?.date) {
        const currentDate = parseISO(props.filters.date)

        // Build params with the new selection
        const params = {
            date: format(currentDate, 'yyyy-MM-dd')
        }

        if (newSelection.length > 0) {
            params.trainers = newSelection.join(',')
        }

        router.get(route('admin.daily-calendar.index'), params, {
            preserveState: false,
            preserveScroll: true
        })
    }
}
</script>
