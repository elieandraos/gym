<template>
    <div class="flex justify-between items-center pb-4">
        <div class="flex flex-wrap grow items-center gap-4">
            <img class="h-16 w-16 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl/8 font-bold text-zinc-950">{{ name }}</h1>
                    <check-badge-icon class="w-6 h-6 text-sky-500 inline" v-if="in_house"></check-badge-icon>
                </div>
                <div class="mt-1 text-sm text-zinc-500 font-medium">{{ age }} years old · Member since {{  since }}</div>
            </div>
        </div>

        <SecondaryButton @click="goToBookingsHistory">View training history</SecondaryButton>
    </div>
</template>

<script setup>
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import { CheckBadgeIcon } from '@heroicons/vue/24/solid/index'

import { router } from '@inertiajs/vue3'

const props = defineProps({
    member: { type: Object, required: true },
})

const { id, name, profile_photo_url, in_house, age, since } = props.member

const goToBookingsHistory = () => router.visit(route('admin.members.history', { user: id }))
</script>
