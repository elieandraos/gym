<template>
    <!-- Table layout for large screens -->
    <table class="min-w-full text-left bg-white hidden lg:table">
        <thead class="text-zinc-400 ">
            <tr>
                <th class="border-b border-b-zinc-200 py-2 font-[500] text-[#71717b]" v-for="header in headers" :key="header">
                    {{ header }}
                </th>
            </tr>
        </thead>
        <tbody>
        <tr v-for="{ id, since, name, profile_photo_url, age, phone_number} in data"
            :key="id"
            class="border-t border-zinc-100 hover:bg-zinc-100 hover:cursor-pointer"
            @click="goToMember(id)">
            <td class="p-3 relative flex gap-2 items-center">
                <img class="h-8 w-8 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                {{ name }}
            </td>
            <td class="text-[#71717b]">
                {{ since }}
            </td>
            <td>
                {{ phone_number }}
            </td>
            <td>
                {{ age }} years old
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Card layout for mobile and tablet: 1 column on mobile, 2 columns on iPad -->
    <ul class="lg:hidden grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <li
            v-for="{ id, since, name, profile_photo_url, age } in data"
            :key="id"
            class="block p-4 rounded-lg border bg-stone-50 border-stone-100 hover:border-stone-200 cursor-pointer"
            @click="goToMember(id)">
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

const goToMember = (id) => router.visit(route('admin.members.show', { user: id }))
</script>
