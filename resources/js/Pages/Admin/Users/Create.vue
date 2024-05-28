<template>
    <AppLayout>
        <Container>
            <page-back-button :url="route('admin.users.index', {'role' : role })">{{ role }}s</page-back-button>
            <user-form></user-form>
        </Container>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
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
