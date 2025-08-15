<template>
    <AppLayout title="Profile">
        <Container>
            <page-title :sticky="true">
                <div class="pb-4 w-full flex justify-between items-center">
                    <div>Trainers list</div>
                    <Link :href="route('admin.trainers.create')">
                        <primary-button type="button" class="">
                            Add trainer
                        </primary-button>
                    </Link>
                </div>
            </page-title>

            <trainers-list :data="data" :headers="headers" :links="meta.links"></trainers-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import TrainersList from '@/Pages/Admin/Trainers/Partials/TrainersList.vue'

const props = defineProps({
    trainers: Object,
})

const { route } = window
const { data, meta } = props.trainers
const headers = ['Name', 'Start date', 'Phone number', 'Age']
</script>
