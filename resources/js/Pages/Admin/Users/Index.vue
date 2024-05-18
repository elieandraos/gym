<template>
    <AppLayout title="Profile">
        <Container>
            <h1 class="text-zinc-900 font-medium text-lg lg:text-xl capitalize pt-12 mb-4 md:pt-5 md:mb-8">{{ role }}s list</h1>
            <users-list :data="data" :headers="headers" :links="meta.links"></users-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Container.vue'
import UsersList from '@/Pages/Admin/Users/Partials/UsersList.vue'

const props = defineProps({
    users: Object,
    role: String,
})

const { data, meta } = props.users
const headers = ['Name', 'Start Date', 'Phone Number', 'Age']
</script>
