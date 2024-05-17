<template>
    <AppLayout title="Profile">
        <Container>
            <div>users list</div>
            <table class="min-w-full text-left text-sm/6">
                <thead class="text-zinc-400">
                    <tr>
                        <th class="border-b border-b-zinc-200 px-4 py-2 font-medium" v-for="header in headers" :key="header">
                            {{ header }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="{ since, name, profile_photo_url, age} in data" :key="name">
                        <td class="text-zinc-900 font-medium p-4 border-b border-zinc-200 relative flex gap-2 items-center">
                            <img class="h-8 w-8 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                            {{ name }}
                        </td>
                        <td class="text-zinc-400 p-4 border-b border-zinc-200">
                            {{ since }}
                        </td>
                        <td class="text-zinc-900 font-medium p-4 border-b border-zinc-200">
                            {{ age }} years old
                        </td>
                    </tr>
                </tbody>
            </table>

            <pagination :links="meta.links"></pagination>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Container.vue'
import Pagination from '@/Components/Pagination.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    users: Array,
})

const { data, meta } = props.users
const headers = ['Name', 'Start Date', 'Age']
</script>
