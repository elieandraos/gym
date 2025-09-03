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

        <Dropdown direction="right">
            <div class="space-y-2 font-normal">
                <Link v-if="isTraining" :href="route('admin.bookings.show', active_booking.id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">View all sessions</Link>
                <Link :href="route('admin.members.history', { user: id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">View training history</Link>
            </div>
        </Dropdown>
    </div>
</template>

<script setup>
import Dropdown from '@/Components/Layout/Dropdown.vue'
import { CheckBadgeIcon } from '@heroicons/vue/24/solid/index'

import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { id, name, profile_photo_url, in_house, age, since, active_booking } = props.member

const isTraining = computed(() => !!active_booking)
</script>
