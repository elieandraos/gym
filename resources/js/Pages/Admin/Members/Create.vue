<template>
    <AppLayout>
        <Container :centered="false">
            <PageHeader :sticky="true">
                <div class="flex items-center gap-4">
                    <PageBackButton />
                    <PageHeaderTitle>New member</PageHeaderTitle>
                </div>
            </PageHeader>
            <MemberForm></MemberForm>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import MemberForm from '@/Pages/Admin/Members/Partials/MemberForm.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

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
