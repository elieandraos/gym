<template>
    <div class="p-4 h-full flex flex-col">
        <div class="grow">
            <!-- logo -->
            <div class="mb-8">
                <Link :href="route('dashboard')">
                    <ApplicationMark class="block h-16 w-auto" />
                </Link>
            </div>

            <!-- menu items -->
            <div class="space-y-2">
                <div v-for="item in menu" :key="item.name">
                    <NavLink :href="item.url" :active="$page.props.url === item.url">
                        <component :is="item.icon" :class="[$page.props.url === item.url ? 'text-zinc-900' : 'text-zinc-500', 'w-5 h-5 group-hover:text-zinc-900 mr-2']"></component>
                        {{  item.name }}
                    </NavLink>
                </div>
            </div>
        </div>

        <!-- logout card -->
        <div class="bg-stone-200 flex items-center px-4 py-2 rounded-lg">
            <div v-if="$page.props.jetstream.managesProfilePhotos && $page.props.auth.user" class="shrink-0 me-3">
                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
            </div>

            <div>
                <div class="font-medium text-sm text-zinc-900">{{ $page.props.auth.user.name }}</div>
                <div>
                    <form @submit.prevent="logout">
                        <button type="submit" class="text-sm text-zinc-500 border-b border-b-zinc-400 hover:text-zinc-900 hover:border-b-zinc-900 lowercase">Logout </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

import ApplicationMark from '@/Components/ApplicationMark.vue'
import NavLink from '@/Components/NavLink.vue'

defineProps({
    menu: { type: Array, required: true },
})

const { route } = window

const logout = () => {
    router.post(route('logout'))
}
</script>
