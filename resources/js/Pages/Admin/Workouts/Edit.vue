<template>
    <AppLayout>
        <Container :centered="false">
            <PageHeader :sticky="true">
                <PageHeaderTitle>Edit Workout</PageHeaderTitle>
            </PageHeader>
            <WorkoutForm></WorkoutForm>
        </Container>
    </AppLayout>
</template>

<script setup>
import WorkoutForm from '@/Pages/Admin/Workouts/Partials/WorkoutForm.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    workout: Object,
    categories: Array,
})

const form = useForm({
    name: props.workout.name,
    categories: props.workout.categories,
})

provide('form', form)
provide('categories', props.categories)
provide('workout', props.workout)
</script>
