<template>
    <div>
        <Head :title="title" ><title>{{ title }}</title></Head>

        <Banner />

        <div class="bg-zinc-100 text-black relative">
            <div class="flex">
                <div class="h-screen w-60 hidden lg:block">
                    <sidebar :menu="menu"></sidebar>
                </div>

                <div class="h-screen lg:pt-2 grow">
                    <main class="h-full overflow-y-scroll bg-white rounded-t-lg lg:ring-1 lg:ring-zinc-950/5 ">
                        <!-- Default mobile navigation -->
                        <div class="lg:hidden">
                            <button @click="toggleSidebar" v-if="!sidebarOpen" class="absolute top-2 right-2 z-50 p-2">
                                <Bars3Icon class="h-6 w-6 text-zinc-700 hover:text-zinc-900" />
                            </button>
                        </div>
                        <!-- main content -->
                        <slot />
                    </main>
                </div>
            </div>

            <floating-sidebar :menu="menu" :sidebar-open="sidebarOpen" @toggle="toggleSidebar"></floating-sidebar>
        </div>
    </div>
</template>

<script setup>

import {
    HomeIcon, UserIcon, UsersIcon, Cog6ToothIcon, PlusCircleIcon, CalendarIcon, ClipboardDocumentListIcon, Bars3Icon
} from '@heroicons/vue/24/solid'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, provide } from 'vue'

import Banner from '@/Components/Layout/Banner.vue'
import FloatingSidebar from '@/Layouts/FloatingSidebar.vue'
import Sidebar from '@/Layouts/Sidebar.vue'

defineProps({
    title: String,
})

// Sidebar state
const sidebarOpen = ref(false)

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

// Provide sidebar functions to child components
provide('toggleSidebar', toggleSidebar)
provide('sidebarOpen', sidebarOpen)

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
