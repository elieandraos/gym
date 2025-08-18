<template>
    <table class="min-w-full text-left hidden lg:table text-sm">
        <thead class="text-zinc-400">
            <tr>
                <th class="border-b border-b-zinc-200 px-4 py-2 text-sm font-medium" v-for="header in headers" :key="header">
                    {{ header }}
                </th>
            </tr>
        </thead>
        <tbody>
        <tr v-for="{ id, name, category } in data"
            :key="id"
            class="border-b border-zinc-100 hover:bg-zinc-100">
            <td class="p-3 relative flex gap-2 items-center">
                {{ name }}
            </td>
            <td class="text-zinc-400 p-4">
                {{ category }}
            </td>
            <td class="p-4">
                <div class="flex gap-2">
                    <Link :href="route('admin.workouts.edit', { workout: id })">
                        <transparent-button class="text-xs">
                            Edit
                        </transparent-button>
                    </Link>
                    <form @submit.prevent="deleteWorkout(id)" class="inline">
                        <transparent-button type="submit" class="text-xs text-red-600 hover:text-red-800">
                            Delete
                        </transparent-button>
                    </form>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <ul class="lg:hidden flex flex-col gap-6">
        <li
            v-for="{ id, name, category } in data"
            :key="id"
            class="block p-4 rounded-lg border bg-stone-50 border-stone-100 hover:border-stone-200">
            <div class="flex items-center justify-between">
                <div class="text-sm space-y-1">
                    <div class="font-medium">{{ name }}</div>
                    <div class="text-zinc-500 text-xs">{{ category }}</div>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.workouts.edit', { workout: id })">
                        <transparent-button class="text-xs">
                            Edit
                        </transparent-button>
                    </Link>
                    <form @submit.prevent="deleteWorkout(id)" class="inline">
                        <transparent-button type="submit" class="text-xs text-red-600 hover:text-red-800">
                            Delete
                        </transparent-button>
                    </form>
                </div>
            </div>
        </li>
    </ul>

    <pagination :links="links"></pagination>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'

import Pagination from '@/Components/Layout/Pagination.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'

const { route } = window

defineProps({
    headers: { type: Array, required: true },
    data: { type: Array, required: true },
    links: { type: Array, required: true },
})

const deleteWorkout = (id) => {
    if (confirm('Are you sure you want to delete this workout?')) {
        router.delete(route('admin.workouts.destroy', { workout: id }))
    }
}
</script>