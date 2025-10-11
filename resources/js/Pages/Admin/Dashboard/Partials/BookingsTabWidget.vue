<template>
    <div class="bg-white rounded-lg p-6">
        <div v-if="loading" class="animate-pulse">
            <div class="flex gap-6 border-b border-zinc-200 mb-4">
                <div class="h-8 bg-zinc-100 rounded w-32"></div>
                <div class="h-8 bg-zinc-100 rounded w-24"></div>
                <div class="h-8 bg-zinc-100 rounded w-32"></div>
            </div>
            <div class="space-y-3">
                <div class="h-12 bg-zinc-100 rounded"></div>
                <div class="h-12 bg-zinc-100 rounded"></div>
                <div class="h-12 bg-zinc-100 rounded"></div>
            </div>
        </div>

        <div v-else>
            <!-- Tab Headers -->
            <div class="flex gap-6 border-b border-zinc-200 mb-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="relative pb-3 text-sm font-[500] transition-colors duration-200"
                    :class="activeTab === tab.id ? 'text-indigo-600' : 'text-zinc-500 hover:text-zinc-700'"
                >
                    <span>{{ tab.label }}</span>
                    <Badge v-if="tab.count > 0" class="ml-2">{{ tab.count }}</Badge>

                    <!-- Animated Bottom Border -->
                    <span
                        class="absolute bottom-0 left-0 right-0 h-0.5 transition-colors duration-200"
                        :class="activeTab === tab.id ? 'bg-indigo-600' : 'bg-transparent'"
                    ></span>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[200px]">
                <!-- Expiring Soon Tab -->
                <div v-if="activeTab === 'expiring'" class="space-y-3">
                    <div
                        v-if="bookings.expiring.length === 0"
                        class="text-center py-8 text-zinc-400"
                    >
                        No bookings expiring soon
                    </div>
                    <div
                        v-for="booking in bookings.expiring"
                        :key="booking.id"
                        class="flex items-center gap-3 py-3 border-b border-zinc-100 last:border-0"
                    >
                        <img
                            :src="booking.member.profile_photo_url"
                            :alt="booking.member.name"
                            class="size-10 rounded-full object-cover flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <Link
                                :href="route('admin.members.show', booking.member.id)"
                                class="text-sm font-[500] text-zinc-900 hover:text-indigo-600"
                            >
                                {{ booking.member.name }}
                            </Link>
                            <p class="text-xs text-zinc-500">{{ booking.nb_remaining_sessions }}</p>
                        </div>
                        <Link
                            :href="route('admin.bookings.create', { member_id: booking.member.id, trainer_id: booking.trainer.id })"
                            class="text-sm text-sky-500 hover:text-sky-700 font-[500] flex-shrink-0"
                        >
                            Renew
                        </Link>
                    </div>
                </div>

                <!-- Unpaid Tab -->
                <div v-if="activeTab === 'unpaid'" class="space-y-3">
                    <div
                        v-if="bookings.unpaid.length === 0"
                        class="text-center py-8 text-zinc-400"
                    >
                        No unpaid bookings
                    </div>
                    <div
                        v-for="booking in bookings.unpaid"
                        :key="booking.id"
                        class="flex items-center gap-3 py-3 border-b border-zinc-100 last:border-0"
                    >
                        <img
                            :src="booking.member.profile_photo_url"
                            :alt="booking.member.name"
                            class="size-10 rounded-full object-cover flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <Link
                                :href="route('admin.members.show', booking.member.id)"
                                class="text-sm font-[500] text-zinc-900 hover:text-indigo-600"
                            >
                                {{ booking.member.name }}
                            </Link>
                            <p class="text-xs text-zinc-500">{{ booking.trainer.name }}</p>
                        </div>
                        <a
                            @click.prevent="markAsPaid(booking.id)"
                            href="#"
                            class="text-sm text-sky-500 hover:text-sky-700 font-[500] flex-shrink-0"
                        >
                            Mark as Paid
                        </a>
                    </div>
                </div>

                <!-- Frozen Tab -->
                <div v-if="activeTab === 'frozen'" class="space-y-3">
                    <div
                        v-if="bookings.frozen.length === 0"
                        class="text-center py-8 text-zinc-400"
                    >
                        No frozen bookings
                    </div>
                    <div
                        v-for="booking in bookings.frozen"
                        :key="booking.id"
                        class="flex items-center gap-3 py-3 border-b border-zinc-100 last:border-0"
                    >
                        <img
                            :src="booking.member.profile_photo_url"
                            :alt="booking.member.name"
                            class="size-10 rounded-full object-cover flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <Link
                                :href="route('admin.members.show', booking.member.id)"
                                class="text-sm font-[500] text-zinc-900 hover:text-indigo-600"
                            >
                                {{ booking.member.name }}
                            </Link>
                            <p class="text-xs text-zinc-500">{{ booking.trainer.name }}</p>
                        </div>
                        <Link
                            :href="route('admin.bookings.unfreeze.index', booking.id)"
                            class="text-sm text-sky-500 hover:text-sky-700 font-[500] flex-shrink-0"
                        >
                            Unfreeze
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import Badge from '@/Components/Layout/Badge.vue'

const { route } = window

const props = defineProps({
    bookings: {
        type: Object,
        default: () => ({
            expiring: [],
            unpaid: [],
            frozen: [],
        }),
    },
    loading: { type: Boolean, default: false },
})

const activeTab = ref('expiring')

const tabs = computed(() => [
    { id: 'expiring', label: 'Expiring Soon', count: props.bookings.expiring?.length || 0 },
    { id: 'unpaid', label: 'Unpaid', count: props.bookings.unpaid?.length || 0 },
    { id: 'frozen', label: 'Frozen', count: props.bookings.frozen?.length || 0 },
])

const markAsPaid = (bookingId) => {
    router.patch(route('admin.bookings.mark-as-paid', bookingId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove from unpaid list
            const index = props.bookings.unpaid.findIndex(b => b.id === bookingId)
            if (index > -1) {
                props.bookings.unpaid.splice(index, 1)
            }
        }
    })
}
</script>
