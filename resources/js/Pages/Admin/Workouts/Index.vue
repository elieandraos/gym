<template>
    <AppLayout title="Profile">
        <Container>
            <page-title :sticky="true">
                <div class="pb-4 w-full flex justify-between items-center">
                    <div>Workouts list</div>
                    <Link :href="route('admin.workouts.create')">
                        <primary-button type="button" class="">
                            Add workout
                        </primary-button>
                    </Link>
                </div>
            </page-title>

            <workouts-list :data="data" :headers="headers" :links="meta?.links || []"></workouts-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import WorkoutsList from '@/Pages/Admin/Workouts/Partials/WorkoutsList.vue'

const props = defineProps({
    workouts: Object,
})

const { route } = window
const { data, meta } = props.workouts || {}
const headers = ['Name', 'Category', 'Actions']
</script>