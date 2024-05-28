<template>
    <AppLayout title="Profile">
        <Container>
            <page-title>
                {{ role }}s list
                <template v-slot:buttons>
                    <Link :href="route('admin.users.create', role)">
                        <primary-button type="button" class="">
                            Add {{ role }}
                        </primary-button>
                    </Link>
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
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UsersList from '@/Pages/Admin/Users/Partials/UsersList.vue'

const props = defineProps({
    users: Object,
    role: String,
})

const { data, meta } = props.users
const headers = ['Name', 'Start Date', 'Phone Number', 'Age']
</script>
