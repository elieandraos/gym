<template>
    <AppLayout title="Calendar">
        <Container>
            <weekly-calendar :weeks="weeks"></weekly-calendar>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import WeeklyCalendar from '@/Components/Layout/WeeklyCalendar.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    weeks: { type: Array, required: true }
})
</script>
