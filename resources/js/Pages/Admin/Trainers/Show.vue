<template>
    <AppLayout title="Personal Information">
        <Container>
            <PageHeader :sticky="true">
                <TrainerHeader :trainer="trainer" />
            </PageHeader>

            <div class="flex flex-col gap-8">
                <user-profile :user="trainer"></user-profile>
                <user-contact :user="trainer"></user-contact>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'
import TrainerHeader from './Partials/TrainerHeader.vue'

const props = defineProps({
    trainer: { type: Object, required: true },
})
</script>
