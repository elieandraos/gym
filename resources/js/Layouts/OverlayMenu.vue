<template>
    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-50 lg:hidden">
            <div
                class="fixed inset-0 bg-zinc-100 bg-opacity-95 transition-opacity duration-300"
                :class="{ 'opacity-100': isOpen, 'opacity-0': !isOpen }"
                @click="$emit('close')"
            ></div>

            <div
                class="fixed inset-0 flex flex-col items-center justify-start bg-zinc-100 p-8 transition-all duration-300"
                :class="{ 'opacity-100 scale-100': isOpen, 'opacity-0 scale-95': !isOpen }"
            >
                <div class="w-full max-w-sm mx-auto">
                    <div class="flex justify-center mb-12 mt-8">
                        <Link :href="route('dashboard')" @click="$emit('close')">
                            <ApplicationMark class="h-16 w-auto text-zinc-900" />
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-center">
                        <div>
                            <h3 class="text-[12px] font-[500] text-[#71717b] capitalize mb-1">Quick Links</h3>
                            <nav class="space-y-3">
                                <Link
                                    v-for="item in quickLinks"
                                    :key="item.name"
                                    :href="item.url"
                                    @click="$emit('close')"
                                    class="block text-zinc-900 hover:text-zinc-600 transition-colors duration-200 p-3 rounded-md text-[16px] hover:bg-zinc-200"
                                >
                                    <component :is="item.icon" class="w-5 h-5 mx-auto mb-1" />
                                    <span class="text-sm">{{ item.name }}</span>
                                </Link>
                            </nav>
                        </div>

                        <div>
                            <h3 class="text-[12px] font-[500] text-[#71717b] capitalize mb-1">Administration</h3>
                            <nav class="space-y-3">
                                <Link
                                    v-for="item in administrationLinks"
                                    :key="item.name"
                                    :href="item.url"
                                    @click="$emit('close')"
                                    class="block text-zinc-900 hover:text-zinc-600 transition-colors duration-200 p-3 text-[16px] rounded-md hover:bg-zinc-200"
                                >
                                    <component :is="item.icon" class="w-5 h-5 mx-auto mb-1" />
                                    <span class="text-sm">{{ item.name }}</span>
                                </Link>
                            </nav>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-zinc-300">
                        <div class="flex items-center justify-center space-x-4 bg-zinc-200 rounded-lg p-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos && $page.props.auth.user" class="shrink-0">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>
                            <div class="text-center">
                                <div class="font-medium text-sm text-zinc-900">{{ $page.props.auth.user.name }}</div>
                                <form @submit.prevent="logout">
                                    <button type="submit" class="text-sm text-zinc-600 hover:text-zinc-900 transition-colors duration-200 mt-1">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import ApplicationMark from '@/Components/ApplicationMark.vue'

const props = defineProps({
    menu: { type: Array, required: true },
    isOpen: { type: Boolean, required: true },
})

defineEmits(['close'])

const { route } = window

const administrationLinks = computed(() => {
    return props.menu.filter(item => ['Members', 'Trainers', 'Workouts', 'Account'].includes(item.name))
})

const quickLinks = computed(() => {
    return props.menu.filter(item => ['Home', 'Private Training', 'Calendar'].includes(item.name))
})

const logout = () => {
    router.post(route('logout'))
}
</script>
