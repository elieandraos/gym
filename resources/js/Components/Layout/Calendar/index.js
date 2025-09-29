// Main views
export { default as WeeklyCalendar } from './views/WeeklyCalendar.vue'

// Reusable components
export { default as CalendarHeader } from './components/CalendarHeader.vue'
export { default as TrainerFilter } from './components/TrainerFilter.vue'
export { default as EventCard } from './components/EventCard.vue'
export { default as EventModal } from './components/EventModal.vue'
export { default as TimeGutter } from './components/TimeGutter.vue'
export { default as DayHeader } from './components/DayHeader.vue'

// Grid components
export { default as WeeklyCalendarGrid } from './views/WeeklyCalendarGrid.vue'

// Composables
export { useCalendarNavigation } from './composables/useCalendarNavigation.js'
export { useTrainerFiltering } from './composables/useTrainerFiltering.js'
export { useCalendarEvents } from './composables/useCalendarEvents.js'
export { useEventPositioning } from './composables/useEventPositioning.js'
export { useEventModal } from './composables/useEventModal.js'
export { useColorScheme } from './composables/useColorScheme.js'