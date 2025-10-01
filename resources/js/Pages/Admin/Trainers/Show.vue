<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true">
                <TrainerHeader :trainer="trainer" />
            </PageHeader>

            <div class="flex flex-col gap-8">
                <div class="flex flex-col md:flex-row md:justify-between gap-6 md:gap-12">
                    <div class="bg-stone-50 w-full p-4 rounded-lg">
                        <user-profile :user="trainer"></user-profile>
                    </div>
                    <div class="bg-stone-50 w-full p-4 rounded-lg">
                        <user-contact :user="trainer"></user-contact>
                    </div>
                </div>

                <div class="text-right">
                    <Link :href="route('admin.trainers.edit', trainer.id)">
                        <PrimaryButton>Edit Information</PrimaryButton>
                    </Link>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'
import TrainerHeader from './Partials/TrainerHeader.vue'

const { route } = window

const props = defineProps({
    trainer: { type: Object, required: true },
})

</script>
