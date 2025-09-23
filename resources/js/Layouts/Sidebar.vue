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
            <div class="space-y-6">
                <!-- Quick Links -->
                <div>
                    <h3 class="text-[12px] font-[500] text-[#71717b] capitalize mb-1">Quick Links</h3>
                    <div class="space-y-1 capitalize leading-[20px] font-[500]">
                        <div v-for="item in quickLinks" :key="item.name">
                            <NavLink :href="item.url" :active="isActive(item)">
                                <component :is="item.icon" :class="[isActive(item) ? 'text-zinc-950' : 'text-zinc-500', 'w-5 h-5 group-hover:text-zinc-950']"></component>
                                {{  item.name }}
                            </NavLink>
                        </div>
                    </div>
                </div>

                <!-- Administration -->
                <div>
                    <h3 class="text-[12px] font-[500] text-[#71717b] capitalize mb-1">Administration</h3>
                    <div class="space-y-1 capitalize leading-[20px] font-[500]">
                        <div v-for="item in administrationLinks" :key="item.name">
                            <NavLink :href="item.url" :active="isActive(item)">
                                <component :is="item.icon" :class="[isActive(item) ? 'text-zinc-950' : 'text-zinc-500', 'w-5 h-5 group-hover:text-zinc-950']"></component>
                                {{  item.name }}
                            </NavLink>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- logout card -->
        <div class="bg-zinc-200 flex items-center px-4 py-2 rounded-lg">
            <div v-if="$page.props.auth.user" class="shrink-0 me-3">
                <img class="h-10 w-10 rounded-full object-cover" :src="profile_photo_url" :alt="name">
            </div>

            <div>
                <div class="font-medium text-sm">{{ name }}</div>
                <div>
                    <form @submit.prevent="logout">
                        <button type="submit" class="text-sm text-zinc-500 border-b border-b-zinc-400 hover:text-zinc-950 hover:border-b-zinc-950 lowercase cursor-pointer">Logout </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

import ApplicationMark from '@/Components/ApplicationMark.vue'
import NavLink from '@/Components/NavLink.vue'

const props = defineProps({
    menu: { type: Array, required: true },
})

const { route } = window
const { name, profile_photo_url } = usePage().props.auth.user

const quickLinks = computed(() => {
    return props.menu.filter(item => ['Home', 'Private Training', 'Calendar'].includes(item.name))
})

const administrationLinks = computed(() => {
    return props.menu.filter(item => ['Members', 'Trainers', 'Workouts', 'Account'].includes(item.name))
})

const logout = () => {
    router.post(route('logout'))
}

const isActive = (item) => {
    const currentRoute = route().current(item.activeRoute)

    if (currentRoute && item.for && route().params.role) if (route().params.role !== item.for) return false

    return currentRoute
}
</script>
