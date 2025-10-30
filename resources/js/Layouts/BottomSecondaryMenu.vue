<template>
    <!-- Backdrop -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="modelValue"
            @click="$emit('update:modelValue', false)"
            class="fixed inset-0 bg-zinc-900/20 z-[60] xl:hidden"
        ></div>
    </Transition>

    <!-- Menu Panel -->
    <Transition
        enter-active-class="transition-transform duration-300 ease-out"
        enter-from-class="translate-y-full"
        enter-to-class="translate-y-0"
        leave-active-class="transition-transform duration-300 ease-in"
        leave-from-class="translate-y-0"
        leave-to-class="translate-y-full"
    >
        <div
            v-if="modelValue"
            class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-2xl shadow-2xl xl:hidden pb-[env(safe-area-inset-bottom)]"
        >
            <!-- Handle bar -->
            <div class="flex justify-center pt-3 pb-2">
                <div class="w-12 h-1 bg-zinc-300 rounded-full"></div>
            </div>

            <!-- Menu Items -->
            <div class="px-4 pb-6 pt-2">
                <div class="space-y-1">
                    <!-- Administration Group -->
                    <Link
                        v-for="item in administrationItems"
                        :key="item.name"
                        :href="item.href"
                        @click="$emit('update:modelValue', false)"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 rounded-lg transition-colors',
                            isActive(item.routeName)
                                ? 'bg-gray-100 text-gray-800 font-semibold'
                                : 'text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        <component :is="item.icon" class="w-6 h-6" />
                        <span class="text-base">{{ item.name }}</span>
                    </Link>

                    <!-- Divider -->
                    <div class="border-t border-zinc-200 my-3"></div>

                    <!-- Setup Group -->
                    <Link
                        v-for="item in setupItems"
                        :key="item.name"
                        :href="item.href"
                        @click="$emit('update:modelValue', false)"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 rounded-lg transition-colors',
                            isActive(item.routeName)
                                ? 'bg-gray-100 text-gray-800 font-semibold'
                                : 'text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        <component :is="item.icon" class="w-6 h-6" />
                        <span class="text-base">{{ item.name }}</span>
                    </Link>

                    <!-- Divider -->
                    <div class="border-t border-zinc-200 my-3"></div>

                    <!-- Logout -->
                    <button
                        @click="logout"
                        class="flex items-center gap-4 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors w-full text-left"
                    >
                        <ArrowLeftStartOnRectangleIcon class="w-6 h-6" />
                        <span class="text-base">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import {
    UserGroupIcon,
    FireIcon,
    UserCircleIcon,
    WrenchIcon,
    ArrowLeftStartOnRectangleIcon,
} from '@heroicons/vue/24/outline'

defineProps({
    modelValue: Boolean,
})

defineEmits(['update:modelValue'])

const administrationItems = [
    {
        name: 'Trainers',
        href: route('admin.trainers.index'),
        routeName: 'admin.trainers.*',
        icon: UserGroupIcon,
    },
    {
        name: 'Workouts',
        href: route('admin.workouts.index'),
        routeName: 'admin.workouts.*',
        icon: FireIcon,
    },
]

const setupItems = [
    {
        name: 'Account',
        href: route('profile.show'),
        routeName: 'profile.show',
        icon: UserCircleIcon,
    },
    {
        name: 'Settings',
        href: route('admin.settings.edit'),
        routeName: 'admin.settings.*',
        icon: WrenchIcon,
    },
]

const isActive = (routeName) => {
    return route().current(routeName)
}

const logout = () => {
    router.post(route('logout'))
}
</script>
