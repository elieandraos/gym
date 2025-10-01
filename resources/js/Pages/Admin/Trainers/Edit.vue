<template>
    <AppLayout>
        <Container>
            <PageHeader :sticky="true">
                <div class="flex items-center gap-4">
                    <PageBackButton />
                    <PageHeaderTitle>Edit Trainer</PageHeaderTitle>
                </div>
            </PageHeader>
            <TrainerForm :is-edit="true"></TrainerForm>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import { useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import TrainerForm from '@/Pages/Admin/Trainers/Partials/TrainerForm.vue'

const props = defineProps({
    trainer: { type: Object, required: true },
})

const form = useForm({
    name: props.trainer.name,
    email: props.trainer.email,
    gender: props.trainer.gender,
    weight: props.trainer.weight,
    height: props.trainer.height,
    blood_type: props.trainer.blood_type,
    birthdate: props.trainer.birthdate,
    phone_number: props.trainer.phone_number,
    instagram_handle: props.trainer.instagram_handle,
    address: props.trainer.address,
    emergency_contact: props.trainer.emergency_contact,
    in_house: props.trainer.in_house,
    registration_date: props.trainer.registration_date,
    color: props.trainer.color,
})

provide('form', form)
provide('userId', props.trainer.id)
</script>
