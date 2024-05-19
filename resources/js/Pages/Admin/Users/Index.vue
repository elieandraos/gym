<template>
    <AppLayout title="Profile">
        <Container>
            <page-title>
                {{ role }}s list
                <template v-slot:buttons>
                    <primary-button type="button" class="">
                        <Link :href="route('dashboard')">Add user</Link>
                    </primary-button>
                </template>
            </page-title>
            <users-list :data="data" :headers="headers" :links="meta.links"></users-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UsersList from '@/Pages/Admin/Users/Partials/UsersList.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    users: Object,
    role: String,
})

const { data, meta } = props.users
const headers = ['Name', 'Start Date', 'Phone Number', 'Age']
</script>
