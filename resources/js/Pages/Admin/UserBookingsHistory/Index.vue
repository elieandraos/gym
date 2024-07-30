<template>
    <AppLayout title="Training History">
        <Container>
            <page-back-button>Back</page-back-button>

            <div class="flex justify-between items-center pb-6 mb-12">
                <div class="grow">
                    <div class="flex gap-x-4">
                        <h1 class="text-xl font-bold text-zinc-950">Training History</h1>
                    </div>
                    <div class="flex gap-x-12 mt-3 text-sm text-zinc-500">
                        <div class="flex gap-x-2">
                            <UserIcon class="w-4 text-zinc-500"></UserIcon>
                            <span>
                                <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.users.show', { user: id, role: 'Member' })"> {{ name}}</Link>
                            </span>
                        </div>
                        <div class="flex gap-x-2">
                            <UsersIcon class="w-4 text-zinc-500"></UsersIcon>
                            <span>{{ trainingsCount}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-sm pb-1 mb-1">Summary</h3>

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
                        @click="goToBooking(id)"
                    >
                        <td class="text-zinc-400 p-4">
                            {{ title }}
                        </td>
                        <td class="text-zinc-900 font-medium p-4 flex gap-2 items-center">
                            <img class="w-8 h-8 rounded-full" :src="trainer.profile_photo_url"  alt=""/>
                            {{ trainer.name }}
                        </td>
                        <td class="text-zinc-900 p-4 font-medium">
                            {{ nb_sessions }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { UsersIcon, UserIcon } from '@heroicons/vue/24/solid/index.js'
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    user: { type: Object, required: true }
})

const { id, name, title, bookings } = props.user

const headers = ['Date', 'Trainer', '# Sessions']

const trainingsCount =  computed( () => {
    if(bookings.length === 0)
        return 'No training history yet'

    return bookings.length > 1 ? bookings.length + ' trainings' : '1 training'
})

const goToBooking = (id) => router.visit( route('admin.bookings.show', id))
</script>
