<template>
    <table class="min-w-full text-left hidden lg:table text-sm">
        <thead class="text-zinc-400">
            <tr>
                <th class="border-b border-b-zinc-200 py-2 text-[#71717b] font-[500]" v-for="header in headers" :key="header">
                    {{ header }}
                </th>
            </tr>
        </thead>
        <tbody>
        <tr v-for="{ id, name, categories } in data"
            :key="id"
            class="border-b border-zinc-100 hover:bg-zinc-100">
            <td class="p-3 relative flex gap-2 items-center">
                {{ name }}
            </td>
            <td class="text-[#71717b]">
                {{ categories?.join(', ') }}
            </td>
            <td>
                <dropdown direction="left">
                    <div class="space-y-2 cursor-pointer">
                        <Link :href="route('admin.workouts.edit', { workout: id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">
                            Edit
                        </Link>
                        <hr class="border-gray-200">
                        <button @click="deleteWorkout(id)" class="cursor-pointer block w-full text-left p-2 text-red-500 hover:bg-red-50 hover:rounded-lg">
                            Delete
                        </button>
                    </div>
                </dropdown>
            </td>
        </tr>
        </tbody>
    </table>

    <ul class="lg:hidden grid grid-cols-1 md:grid-cols-2 gap-6">
        <li
            v-for="{ id, name, categories } in data"
            :key="id"
            class="block p-4 rounded-lg border bg-stone-50 border-stone-100 hover:border-stone-200">
            <div class="flex items-center justify-between">
                <div class="text-sm space-y-1">
                    <div class="font-medium">{{ name }}</div>
                    <div class="text-zinc-500 text-xs">{{ categories?.join(', ') }}</div>
                </div>
                <dropdown direction="left">
                    <div class="space-y-2">
                        <Link :href="route('admin.workouts.edit', { workout: id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">
                            Edit
                        </Link>
                        <hr class="border-gray-200">
                        <button @click="deleteWorkout(id)" class="block w-full text-left p-2 text-red-500 hover:bg-red-50 hover:rounded-lg">
                            Delete
                        </button>
                    </div>
                </dropdown>
            </div>
        </li>
    </ul>

    <pagination :links="links"></pagination>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'

import Pagination from '@/Components/Layout/Pagination.vue'
import Dropdown from '@/Components/Layout/Dropdown.vue'

const { route } = window

defineProps({
    headers: { type: Array, required: true },
    data: { type: Array, required: true },
    links: { type: Array, required: true },
})

const deleteWorkout = (id) => {
    if (confirm('Are you sure you want to delete this workout?')) {
        router.visit(route('admin.workouts.destroy', { workout: id }), {
            method: 'delete',
        })
    }
}
</script>
