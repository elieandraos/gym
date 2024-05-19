<template>
    <AppLayout title="Profile">
        <Container>
            <page-title>{{ role }}s list</page-title>
            <users-list :data="data" :headers="headers" :links="meta.links"></users-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Container.vue'
import PageTitle from '@/Components/PageTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UsersList from '@/Pages/Admin/Users/Partials/UsersList.vue'

const props = defineProps({
    users: Object,
    role: String,
})

const { data, meta } = props.users
const headers = ['Name', 'Start Date', 'Phone Number', 'Age']
</script>
