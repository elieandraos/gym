<template>
    <AppLayout title="Member Booking History">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <table class="min-w-full text-left lg:table sm:table hidden">
                <thead class="text-[#71717b]">
                    <tr>
                        <th class="py-2 border-b-zinc-200 font-[500]" v-for="header in headers" :key="header">
                            {{ header }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="{ id, title, nb_sessions, trainer, is_paid } in bookings"
                        :key="id"
                        class="border-b border-zinc-100 hover:bg-zinc-100 hover:cursor-pointer"
                        @click="goToBooking(id)"
                    >
                        <td class="text-zinc-400 p-3">
                            <div class="flex items-center gap-2">
                                {{ title }}
                                <Badge :type="is_paid ? 'success' : 'error'">
                                    {{ is_paid ? 'paid' : 'unpaid' }}
                                </Badge>
                            </div>
                        </td>
                        <td class="flex gap-2 items-center mt-2">
                            <img class="size-8 rounded-full" :src="trainer.profile_photo_url"  alt=""/>
                            <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-sky-500 hover:text-sky-700 font-[400]">{{ trainer.name }}</Link>
                        </td>
                        <td>{{ nb_sessions }}</td>
                    </tr>
                </tbody>
            </table>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

import Badge from '@/Components/Layout/Badge.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const {
    completed_bookings: bookings,
} = props.member

const headers = ['Date', 'Trainer', '# Sessions']

const { route } = window

const goToBooking = (bookingId) => router.visit(route('admin.bookings.show', bookingId))
</script>
