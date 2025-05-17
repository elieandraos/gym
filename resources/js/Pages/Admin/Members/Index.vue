<template>
    <AppLayout title="Profile">
        <Container>
            <page-title>
                Members list
                <template v-slot:buttons>
                    <Link :href="route('admin.members.create')">
                        <primary-button type="button" class="">
                            Add member
                        </primary-button>
                    </Link>
                </template>
            </page-title>

            <members-list :data="data" :headers="headers" :links="meta.links"></members-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MembersList from '@/Pages/Admin/Members/Partials/MembersList.vue'

const props = defineProps({
    members: Object,
})

const { route } = window
const { data, meta } = props.members
const headers = ['Name', 'Start date', 'Phone number', 'Age']
</script>
