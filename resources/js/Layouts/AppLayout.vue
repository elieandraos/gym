<template>
    <div>
        <Head :title="title" ><title>{{ title }}</title></Head>

        <FlashBanner />

        <div class="bg-zinc-100 text-black relative">
            <div class="flex">
                <div class="h-screen w-60 hidden xl:block">
                    <sidebar :menu="menu"></sidebar>
                </div>

                <div class="h-screen xl:pt-2 grow">
                    <main class="h-full overflow-y-scroll bg-white rounded-t-lg xl:ring-1 xl:ring-zinc-950/5 pb-20 xl:pb-0">
                        <!-- main content -->
                        <slot />
                    </main>
                </div>
            </div>

            <!-- Bottom Toolbar (mobile/tablet only) -->
            <BottomToolbar @toggle-menu="secondaryMenuOpen = !secondaryMenuOpen" />

            <!-- Secondary Menu (mobile/tablet only) -->
            <BottomSecondaryMenu v-model="secondaryMenuOpen" />
        </div>
    </div>
</template>

<script setup>

import {
    HomeIcon, UserIcon, UsersIcon, UserCircleIcon, WrenchIcon, PlusCircleIcon, CalendarIcon, FireIcon
} from '@heroicons/vue/24/solid'
import { Head, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

import BottomSecondaryMenu from '@/Layouts/BottomSecondaryMenu.vue'
import BottomToolbar from '@/Layouts/BottomToolbar.vue'
import FlashBanner from '@/Components/Layout/FlashBanner.vue'
import Sidebar from '@/Layouts/Sidebar.vue'

defineProps({
    title: String,
})

const { route } = window

// Secondary menu state
const secondaryMenuOpen = ref(false)

const menu = [
    {
        name: 'Home',
        url: route('dashboard'),
        icon: HomeIcon,
        activeRoute: 'dashboard',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Private Training',
        url: route('admin.bookings.create'),
        icon: PlusCircleIcon,
        activeRoute: 'admin.bookings.create',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Calendar',
        url: route('admin.weekly-calendar.index'),
        icon: CalendarIcon,
        activeRoute: 'admin.weekly-calendar.index',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Members',
        url: route('admin.members.index'),
        icon: UserIcon,
        activeRoute: 'admin.members.*',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Trainers',
        url: route('admin.trainers.index'),
        icon: UsersIcon,
        activeRoute: 'admin.trainers.*',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Account',
        url: route('profile.show'),
        icon: UserCircleIcon,
        activeRoute: 'profile.show',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Settings',
        url: route('admin.settings.edit'),
        icon: WrenchIcon,
        activeRoute: 'admin.settings.*',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Workouts',
        url: route('admin.workouts.index'),
        icon: FireIcon,
        activeRoute: 'admin.workouts.*',
        when: () => usePage().props.auth.user,
    },
]
</script>
