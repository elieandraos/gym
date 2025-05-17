<template>
    <AppLayout>
        <Container :centered="true">
            <page-title>
                <div class="pb-8 border-b border-zinc-200 w-full">New Trainer</div>
            </page-title>
            <trainer-form></trainer-form>
        </Container>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import TrainerForm from '@/Pages/Admin/Trainers/Partials/TrainerForm.vue'

const { route } = window

const form = useForm({
    name: null,
    email: null,
    gender: null,
    weight: null,
    height: null,
    blood_type: null,
    birthdate: '1993-9-27',
    phone_number: null,
    instagram_handle: null,
    address: null,
    emergency_contact: null,
    in_house: true,
    registration_date: new Date(),
})

provide('form', form)
</script>
