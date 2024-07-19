<template>
    <div>
        <Head :title="title" ><title>{{ title }}</title></Head>

        <Banner />

        <div class="bg-zinc-100 text-black relative">
            <div class="flex">
                <div class="h-screen w-64 hidden lg:block">
                    <sidebar :menu="menu"></sidebar>
                </div>
                <div class="h-screen lg:pt-2 grow">
                    <main class="h-full overflow-y-scroll bg-white rounded-t-lg lg:ring-1 lg:ring-zinc-950/5 ">
                        <div class="h-6 lg:hidden"></div>
                        <slot />
                    </main>
                </div>
            </div>
            <floating-sidebar :menu="menu"></floating-sidebar>
        </div>
    </div>
</template>

<script setup>
import {
    HomeIcon, UserIcon, UsersIcon, Cog6ToothIcon, PlusCircleIcon,
} from '@heroicons/vue/24/solid'
import { Head, usePage } from '@inertiajs/vue3'

import Banner from '@/Components/Layout/Banner.vue'
import FloatingSidebar from '@/Layouts/FloatingSidebar.vue'
import Sidebar from '@/Layouts/Sidebar.vue'

defineProps({
    title: String,
})

const { route } = window

const menu = [
    {
        name: 'Home',
        url: route('dashboard'),
        icon: HomeIcon,
        activeRoute: 'dashboard',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Training',
        url: route('admin.bookings.create'),
        icon: PlusCircleIcon,
        activeRoute: 'admin.bookings.create',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Members',
        url: route('admin.users.index', { role: 'Member' }),
        icon: UserIcon,
        activeRoute: 'admin.users.*',
        for: 'Member',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Trainers',
        url: route('admin.users.index', { role: 'Trainer' }),
        icon: UsersIcon,
        activeRoute: 'admin.users.*',
        for: 'Trainer',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Account',
        url: route('profile.show'),
        icon: Cog6ToothIcon,
        activeRoute: 'profile.show',
        when: () => usePage().props.auth.user,
    },
]
</script>
