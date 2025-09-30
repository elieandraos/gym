<template>
    <AppLayout title="Calendar">
        <div class="h-full overflow-hidden">
            <Container>
                <weekly-calendar
                    :events="events"
                    :is_current="is_current"
                    :available_trainers="available_trainers"
                    :filters="filters">
                </weekly-calendar>
            </Container>
        </div>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import WeeklyCalendar from '@/Components/Layout/Calendar/views/WeeklyCalendar.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    events: { type: Array, required: true },
    is_current: { type: Boolean, required: true },
    available_trainers: { type: Array, required: true },
    filters: { type: Object, required: true }
})
</script>
