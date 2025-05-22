<template>
    <AppLayout title="Calendar">
        <Container>
            <h2 class="text-2xl/8 font-medium text-zinc-950 sm:text-xl/8">This week's calendar</h2>
            <pre>{{ weeks }}</pre>
            <weekly-calendar></weekly-calendar>
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
