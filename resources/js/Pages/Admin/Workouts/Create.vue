<template>
    <AppLayout>
        <Container :centered="false">
            <page-title :sticky="true">
                <div class="pb-4 border-b border-zinc-200 w-full mb-8">New Workout</div>
            </page-title>
            <workout-form></workout-form>
        </Container>
    </AppLayout>
</template>

<script setup>
import WorkoutForm from '@/Pages/Admin/Workouts/Partials/WorkoutForm.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
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