<template>
    <AppLayout>
        <Container :centered="true">
            <page-title>
                <div class="pb-8 border-b border-zinc-200 w-full">New {{ role }}</div>
            </page-title>
            <user-form></user-form>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageTitle from '@/Components/Layout/PageTitle.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserForm from '@/Pages/Admin/Users/Partials/UserForm.vue'

const { route } = window

const props = defineProps({
    role: { type: String, default: 'Member', required: false },
})

const form = useForm({
    name: null,
    email: null,
    gender: null,
    weight: null,
    height: null,
    blood_type: null,
    birthdate: null,
    role: props.role,
    phone_number: null,
    instagram_handle: null,
    address: null,
    emergency_contact: null,
    in_house: true,
    registration_date: new Date(),
})

provide('form', form)
</script>
