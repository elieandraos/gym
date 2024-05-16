<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-stone-100 text-black relative">
            <div class="flex">
                <div class="h-screen w-60 hidden lg:block">
                    <sidebar :menu="menu"></sidebar>
                </div>
                <div class="grow bg-white rounded-t-lg lg:mt-3">
                    <slot />
                </div>
            </div>
            <floating-sidebar :menu="menu"></floating-sidebar>
        </div>
    </div>
</template>

<script setup>
import {
    HomeIcon, UserIcon, UsersIcon, Cog6ToothIcon,
} from '@heroicons/vue/24/solid'
import { Head, usePage } from '@inertiajs/vue3'

import Banner from '@/Components/Banner.vue'
import FloatingSidebar from '@/Layouts/FloatingSidebar.vue'
import Sidebar from '@/Layouts/Sidebar.vue'

defineProps({
    title: String,
})

const { route } = window

const menu = [
    {
        name: 'Dashboard',
        url: route('dashboard'),
        icon: HomeIcon,
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Members',
        url: route('admin.users.index', { role: 'Member' }),
        icon: UserIcon,
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Trainers',
        url: route('admin.users.index', { role: 'Trainer' }),
        icon: UsersIcon,
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Account',
        url: route('profile.show'),
        icon: Cog6ToothIcon,
        when: () => usePage().props.auth.user,
    },
]
</script>
