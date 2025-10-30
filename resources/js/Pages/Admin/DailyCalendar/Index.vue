<template>
    <AppLayout title="Daily Calendar">
        <div class="h-full overflow-hidden">
            <Container>
                <daily-calendar
                    :events="events"
                    :is_today="is_today"
                    :available_trainers="available_trainers"
                    :filters="filters"
                    :start-hour="calendar_settings.start_hour"
                    :end-hour="calendar_settings.end_hour">
                </daily-calendar>
            </Container>
        </div>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import DailyCalendar from '@/Components/Layout/Calendar/views/DailyCalendar.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    events: { type: Array, required: true },
    is_today: { type: Boolean, required: true },
    available_trainers: { type: Array, required: true },
    filters: { type: Object, required: true },
    calendar_settings: { type: Object, required: true }
})
</script>