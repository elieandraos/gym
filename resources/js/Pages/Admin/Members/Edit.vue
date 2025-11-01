<template>
    <AppLayout>
        <Container :centered="false">
            <PageHeader :sticky="true">
                <div class="flex items-center gap-4">
                    <PageBackButton />
                    <PageHeaderTitle>Edit member</PageHeaderTitle>
                </div>
            </PageHeader>
            <MemberForm :is-edit="true"></MemberForm>
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

const props = defineProps({
    member: { type: Object, required: true },
})

const form = useForm({
    name: props.member.name,
    email: props.member.email,
    gender: props.member.gender,
    weight: props.member.weight,
    height: props.member.height,
    blood_type: props.member.blood_type,
    birthdate: props.member.birthdate,
    phone_number: props.member.phone_number,
    instagram_handle: props.member.instagram_handle,
    address: props.member.address,
    emergency_contact: props.member.emergency_contact,
    lead_source: props.member.lead_source,
    registration_date: props.member.registration_date,
    color: props.member.color || 'bg-blue-50',
    photo: null,
    remove_photo: false,
})

provide('form', form)
provide('userId', props.member.id)
provide('profilePhotoUrl', props.member.profile_photo_url)
provide('profilePhotoPath', props.member.profile_photo_path)
</script>
