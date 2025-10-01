<template>
    <AppLayout title="Personal Information">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="flex flex-col gap-8">
                <user-profile :user="member"></user-profile>
                <user-contact :user="member"></user-contact>

                <div class="text-right">
                    <Link :href="route('admin.members.edit', member.id)">
                        <PrimaryButton>Edit Information</PrimaryButton>
                    </Link>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})
</script>