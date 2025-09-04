<template>
    <AppLayout title="Profile">
        <Container>
            <div class="flex justify-between items-center pb-6">
                <div class="flex flex-wrap grow items-center gap-6">
                    <img class="h-20 w-20 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-bold text-zinc-950">{{ name }}</h1>
                            <check-badge-icon class="w-6 h-6 text-sky-500 inline" v-if="in_house"></check-badge-icon>
                        </div>
                        <div class="mt-1 text-sm text-zinc-500">{{ age }} years old · Member since {{  since }}</div>
                    </div>
                </div>
            </div>

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
import { CheckBadgeIcon } from '@heroicons/vue/24/solid'
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserContact from '@/Pages/Admin/Users/Partials/UserContact.vue'
import UserProfile from '@/Pages/Admin/Users/Partials/UserProfile.vue'

const props = defineProps({
    trainer: { type: Object, required: true },
})

const {
    name, since, profile_photo_url, in_house, age
} = props.trainer
</script>
