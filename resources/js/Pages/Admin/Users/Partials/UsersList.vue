<template>
    <table class="min-w-full text-left text-sm/6 hidden lg:table">
        <thead class="text-zinc-400">
        <tr>
            <th class="border-b border-b-zinc-200 px-4 py-2 font-medium" v-for="header in headers" :key="header">
                {{ header }}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="{ id, since, name, profile_photo_url, age, phone_number, role} in data"
            :key="id" class="border-b border-zinc-200 hover:bg-stone-100 hover:cursor-pointer"
            @click="goToUser(id, role)"
        >
            <td class="text-zinc-900 font-medium p-4 relative flex gap-2 items-center">
                <img class="h-8 w-8 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                {{ name }}
            </td>
            <td class="text-zinc-400 p-4">
                {{ since }}
            </td>
            <td class="text-zinc-900 font-medium p-4">
                {{ phone_number }}
            </td>
            <td class="text-zinc-900 font-medium p-4">
                {{ age }} years old
            </td>
        </tr>
        </tbody>
    </table>

    <ul class="lg:hidden flex flex-col gap-6">
        <li
            v-for="{ id, since, name, profile_photo_url, age, role } in data"
            :key="id"
            class="block p-4 rounded-lg border bg-stone-50 border-stone-100 hover:border-stone-200 cursor-pointer"
            @click="goToUser(id, role)">
            <div class="flex items-center gap-4">
                <img class="h-12 w-12 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                <div class="text-sm space-y-1">
                    <div class="font-medium">{{ name }}</div>
                    <div class="text-xs">{{ age }} years old</div>
                    <div class="text-zinc-500 text-xs">started on {{ since }}</div>
                </div>
            </div>
        </li>
    </ul>

    <pagination :links="links"></pagination>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

import Pagination from '@/Components/Layout/Pagination.vue'

const { route } = window

defineProps({
    headers: { type: Array, required: true },
    data: { type: Array, required: true },
    links: { type: Array, required: true },
})

const goToUser = (id, role) => router.visit(route('admin.users.show', { user: id, role }))
</script>
