<template>
    <div>
        <Head :title="title" ><title>{{ title }}</title></Head>

        <FlashBanner />

        <div class="bg-zinc-100 text-black relative">
            <div class="flex">
                <div class="h-screen w-60 hidden lg:block">
                    <sidebar :menu="menu"></sidebar>
                </div>

                <div class="h-screen lg:pt-2 grow">
                    <main class="h-full overflow-y-scroll bg-white rounded-t-lg lg:ring-1 lg:ring-zinc-950/5 ">
                        <!-- Mobile menu toggle -->
                        <div class="lg:hidden">
                            <div class="absolute top-1 right-1 z-[60]">
                                <AnimatedMenuIcon :is-open="overlayOpen" @toggle="toggleOverlay" />
                            </div>
                        </div>
                        <!-- main content -->
                        <slot />
                    </main>
                </div>
            </div>

            <OverlayMenu :menu="menu" :is-open="overlayOpen" @close="toggleOverlay" />
        </div>
    </div>
</template>

<script setup>

import {
    HomeIcon, UserIcon, UsersIcon, Cog6ToothIcon, PlusCircleIcon, CalendarIcon, ClipboardDocumentListIcon
} from '@heroicons/vue/24/solid'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, provide } from 'vue'

import AnimatedMenuIcon from '@/Components/Layout/AnimatedMenuIcon.vue'
import FlashBanner from '@/Components/Layout/FlashBanner.vue'
import OverlayMenu from '@/Layouts/OverlayMenu.vue'
import Sidebar from '@/Layouts/Sidebar.vue'

defineProps({
    title: String,
})

// Overlay state
const overlayOpen = ref(false)

const toggleOverlay = () => {
    overlayOpen.value = !overlayOpen.value
}

// Provide overlay functions to child components
provide('toggleOverlay', toggleOverlay)
provide('overlayOpen', overlayOpen)

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
        name: 'Workouts',
        url: route('admin.workouts.index'),
        icon: ClipboardDocumentListIcon,
        activeRoute: 'admin.workouts.*',
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
