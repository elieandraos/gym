<template>
    <AppLayout>
        <Container :centered="false">
            <page-header :sticky="true">
                <div class="pb-4 border-b border-zinc-200 w-full mb-8">Edit Workout</div>
            </page-header>
            <workout-form></workout-form>
        </Container>
    </AppLayout>
</template>

<script setup>
import WorkoutForm from '@/Pages/Admin/Workouts/Partials/WorkoutForm.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    workout: Object,
    categories: Array,
})

const form = useForm({
    name: props.workout.name,
    category: props.workout.category,
})

provide('form', form)
provide('categories', props.categories)
provide('workout', props.workout)
</script>