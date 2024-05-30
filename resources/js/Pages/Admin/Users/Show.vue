<template>
    <AppLayout title="Profile">
        <Container>
            <page-back-button>{{ role }}s</page-back-button>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-16">
                <div class="flex flex-col gap-12 lg:w-2/5">
                    <user-profile :user="user"></user-profile>
                    <user-contact :user="user"></user-contact>
                </div>
                <div class="lg:w-3/5">
                    <member-training-status :user="user"></member-training-status>
                    {{ trainer_bookings }}
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MemberTrainingStatus from '@/Pages/Admin/Users/Partials/MemberTrainingStatus.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const props = defineProps({
    user: { type: Object, required: true },
})

const {
    role, trainer_bookings,
} = props.user
</script>
