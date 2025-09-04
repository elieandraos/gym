<template>
    <AppLayout title="Profile">
        <Container>
            <trainer-header :trainer="trainer" :bordered="true"  />

            <div class="flex flex-col md:flex-row md:justify-between gap-6 md:gap-12">
                <div class="bg-stone-50 w-full p-4 rounded-lg">
                    <user-profile :user="trainer"></user-profile>
                </div>
                <div class="bg-stone-50 w-full p-4 rounded-lg">
                    <user-contact :user="trainer"></user-contact>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'
import TrainerHeader from './Partials/TrainerHeader.vue'

const props = defineProps({
    trainer: { type: Object, required: true },
})

</script>
