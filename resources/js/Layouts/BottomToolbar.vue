<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-zinc-200 xl:hidden pb-[env(safe-area-inset-bottom)]">
        <div class="flex items-center justify-around h-16 px-2">
            <Link
                v-for="item in navItems"
                :key="item.name"
                :href="item.href"
                :class="[
                    'flex items-center justify-center flex-1 h-full transition-colors duration-150',
                    isActive(item.routeName)
                        ? 'text-gray-800'
                        : 'text-gray-400 hover:text-gray-600'
                ]"
            >
                <component
                    :is="isActive(item.routeName) ? item.iconSolid : item.icon"
                    class="w-7 h-7"
                />
            </Link>

            <!-- More Menu Button -->
            <button
                @click="$emit('toggle-menu')"
                class="flex items-center justify-center flex-1 h-full transition-colors duration-150 text-gray-400 hover:text-gray-600"
            >
                <EllipsisHorizontalIcon class="w-7 h-7 stroke-[2]" />
            </button>
        </div>
    </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

import {
    HomeIcon,
    CalendarDaysIcon,
    PlusCircleIcon,
    UsersIcon,
    EllipsisHorizontalIcon,
} from '@heroicons/vue/24/outline'

import {
    HomeIcon as HomeIconSolid,
    CalendarDaysIcon as CalendarDaysIconSolid,
    PlusCircleIcon as PlusCircleIconSolid,
    UsersIcon as UsersIconSolid,
} from '@heroicons/vue/24/solid'

defineEmits(['toggle-menu'])

const page = usePage()

const navItems = [
    {
        name: 'Home',
        href: route('dashboard'),
        routeName: 'dashboard',
        icon: HomeIcon,
        iconSolid: HomeIconSolid,
    },
    {
        name: 'Calendar',
        href: route('admin.weekly-calendar.index'),
        routeName: 'admin.weekly-calendar.*',
        icon: CalendarDaysIcon,
        iconSolid: CalendarDaysIconSolid,
    },
    {
        name: 'Training',
        href: route('admin.bookings.create'),
        routeName: 'admin.bookings.*',
        icon: PlusCircleIcon,
        iconSolid: PlusCircleIconSolid,
    },
    {
        name: 'Members',
        href: route('admin.members.index'),
        routeName: 'admin.members.*',
        icon: UsersIcon,
        iconSolid: UsersIconSolid,
    },
]

const isActive = (routeName) => {
    return route().current(routeName)
}
</script>
