<template>
    <div>
        <!-- Hamburger Menu Button -->
        <button @click="toggleSidebar" class="absolute top-4 left-4 lg:hidden z-30" v-if="!sidebarOpen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>

        <!-- Overlay -->
        <div v-if="sidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300"></div>

        <!-- Sidebar -->
        <div :class="['fixed top-0 min-h-screen w-60 bg-white rounded-tr-lg rounded-br-lg p-4 transform transition-transform duration-300 z-50', sidebarOpen ? 'translate-x-0' : '-translate-x-full']">
            <div class="mb-8">
                <Link :href="route('dashboard')">
                    <ApplicationMark class="block h-12 w-auto mx-auto mt-3" />
                </Link>
            </div>
            <div class="space-y-2">
                <div v-for="item in menu" :key="item.name">
                    <NavLink :href="item.url" :active="$page.props.url === item.url">
                        <component :is="item.icon" :class="[$page.props.url === item.url ? 'text-zinc-900' : 'text-zinc-500', 'w-5 h-5 group-hover:text-zinc-900 mr-2']"></component>
                        {{  item.name }}
                    </NavLink>
                </div>
            </div>
        </div>

        <div>
            <form @submit.prevent="logout">
                <span @click="logout">{{ $page.props.auth.user.name }} Logout </span>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

import ApplicationMark from '@/Components/ApplicationMark.vue'
import NavLink from '@/Components/NavLink.vue'

defineProps({
    menu: { type: Array, required: true },
})

const sidebarOpen = ref(false)

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

const logout = () => {
    router.post(route('logout'))
}
</script>
