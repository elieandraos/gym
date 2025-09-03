<template>
    <AppLayout title="Member Booking History">
        <Container>
            <page-title :sticky="true">
                <member-header :member="member"></member-header>
            </page-title>

            <table class="min-w-full text-left lg:table text-sm">
                <thead class="text-zinc-400">
                <tr>
                    <th class="border-b border-b-zinc-200 px-4 py-2 text-sm font-medium" v-for="header in headers" :key="header">
                        {{ header }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="{ id, title, nb_sessions, trainer } in bookings"
                    :key="id"
                    class="border-b border-zinc-100 hover:bg-zinc-100 hover:cursor-pointer"
                    @click="goToBooking(id)">
                    <td class="text-zinc-400 p-4">
                        {{ title }}
                    </td>
                    <td class="text-zinc-900 font-medium p-4 flex gap-2 items-center">
                        <img class="w-8 h-8 rounded-full" :src="trainer.profile_photo_url"  alt=""/>
                        <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-sky-500 hover:text-sky-700 font-medium">{{ trainer.name }}</Link>
                    </td>
                    <td class="text-zinc-900 p-4 font-medium">
                        {{ nb_sessions }}
                    </td>
                </tr>
                </tbody>
            </table>
        </Container>
    </AppLayout>
</template>

<script setup>
import { UsersIcon, UserIcon } from '@heroicons/vue/24/solid'
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const {
    id, name, completed_bookings: bookings,
} = props.member

const headers = ['Date', 'Trainer', '# Sessions']

const { route } = window

const goToBooking = (bookingId) => router.visit(route('admin.bookings.show', bookingId))
</script>
