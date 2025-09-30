<template>
    <div class="flex h-[calc(100vh-4rem)] flex-col">
        <!-- Calendar Header with navigation and filters -->
        <WeeklyCalendarHeader
            :label="monthLabel"
            :header-days="headerDays"
            @prev-click="prevWeek"
            @next-click="nextWeek"
        >
            <template #filters>
                <TrainerFilter
                    v-model="selectedTrainers"
                    :available-trainers="availableTrainers"
                    @filter-change="handleFilterChange"
                    @update:modelValue="updateTrainerSelection"
                />
            </template>
        </WeeklyCalendarHeader>

        <!-- Weekly Grid -->
        <WeeklyCalendarGrid
            :header-days="headerDays"
            :hours="hours"
            :filtered-events="filteredEvents"
            :start-hour="startHour"
            :end-hour="endHour"
            :auto-scroll-to-time="autoScrollToTime"
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
import WeeklyCalendarHeader from '../components/WeeklyCalendarHeader.vue'
import TrainerFilter from '../components/TrainerFilter.vue'
import WeeklyCalendarGrid from './WeeklyCalendarGrid.vue'
import EventModal from '../components/EventModal.vue'

// Composables
import { useCalendarNavigation } from '../composables/useCalendarNavigation.js'
import { useTrainerFiltering } from '../composables/useTrainerFiltering.js'
import { useCalendarEvents } from '../composables/useCalendarEvents.js'
import { useEventPositioning } from '../composables/useEventPositioning.js'
import { useEventModal } from '../composables/useEventModal.js'

const props = defineProps({
    events: Array,
    is_current: Boolean,
    available_trainers: Array,
    filters: { type: Object, default: () => ({}) },
    startHour: { type: Number, default: 6 },
    endHour: { type: Number, default: 22 },
    autoScrollToTime: { type: Boolean, default: true }
})

const { route } = window

// Set up trainer filtering state first
const { selectedTrainers } = useTrainerFiltering(
    props.filters,
    null, // getNavParams not needed for state setup
    'admin.weekly-calendar.index'
)

// Set up navigation with trainer filtering
const { prevWeek, nextWeek } = useCalendarNavigation(
    props.filters,
    selectedTrainers,
    'admin.weekly-calendar.index'
)

// Set up calendar events
const { monthLabel, headerDays, hours, rawMerged } = useCalendarEvents(
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

    if (props.filters?.start && props.filters?.end) {
        const currentStart = parseISO(props.filters.start)
        const currentEnd = parseISO(props.filters.end)

        // Build params with the new selection
        const params = {
            start: format(currentStart, 'yyyy-MM-dd'),
            end: format(currentEnd, 'yyyy-MM-dd')
        }

        if (newSelection.length > 0) {
            params.trainers = newSelection.join(',')
        }

        router.get(route('admin.weekly-calendar.index'), params, {
            preserveState: false,
            preserveScroll: true
        })
    }
}

// Apply filter function that uses current filters
const handleFilterChange = () => {
    // This is now handled by updateTrainerSelection
}
</script>
