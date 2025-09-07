<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true" :bordered="!isTraining" :bottom-gap="!isTraining">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <TrainingStatus :member="member"  v-if="isTraining" class="mb-12"></TrainingStatus>

            <MemberTrainingNotifications :member="member" class="mb-12"></MemberTrainingNotifications>

            <div class="flex flex-col md:flex-row md:justify-between gap-6 md:gap-12">
                <div class="bg-stone-50 w-full p-4 rounded-lg">
                    <user-profile :user="member"></user-profile>
                </div>
                <div class="bg-stone-50 w-full p-4 rounded-lg">
                    <user-contact :user="member"></user-contact>
                </div>
            </div>

        </Container>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import MemberTrainingNotifications from '@/Pages/Admin/Members/Partials/MemberTrainingNotifications.vue'
import TrainingStatus from '@/Pages/Admin/Members/Partials/TrainingStatus.vue'
import Container from '@/Components/Layout/Container.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

import { computed } from 'vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking } = props.member

const isTraining = computed(() => !!active_booking)
</script>
