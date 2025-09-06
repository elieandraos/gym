<template>
    <AppLayout>
        <Container>
            <PageHeader :sticky="true">
                <PageHeaderTitle>New Workout</PageHeaderTitle>
            </PageHeader>
            <WorkoutForm></WorkoutForm>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import WorkoutForm from '@/Pages/Admin/Workouts/Partials/WorkoutForm.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    categories: Array,
})

const form = useForm({
    name: null,
    category: null,
})

provide('form', form)
provide('categories', props.categories)
</script>
