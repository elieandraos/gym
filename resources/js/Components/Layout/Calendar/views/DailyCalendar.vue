<template>
    <div class="flex h-full flex-col">
        <!-- Daily Calendar Header with navigation and filters -->
        <DailyCalendarHeader
            :date-label="dateLabel"
            @prev-click="prevDay"
            @next-click="nextDay"
        >
            <template #filters>
                <TrainerFilter
                    v-model="selectedTrainers"
                    :available-trainers="availableTrainers"
                    @filter-change="handleFilterChange"
                    @update:modelValue="updateTrainerSelection"
                />
            </template>
        </DailyCalendarHeader>

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
import DailyCalendarHeader from '../components/DailyCalendarHeader.vue'
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
