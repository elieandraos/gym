<template>
    <div class="bg-zinc-50 rounded-lg p-6 space-y-6">
        <!-- Unpaid Section -->
        <div>
            <h3 class="text-sm font-[600] text-zinc-900 mb-4">
                Unpaid
                <Badge v-if="unpaidBookings.length > 0" type="error" class="ml-2">{{ unpaidBookings.length }}</Badge>
            </h3>
            <div v-if="loading" class="animate-pulse space-y-3">
                <div class="h-12 bg-zinc-100 rounded"></div>
                <div class="h-12 bg-zinc-100 rounded"></div>
                <div class="h-12 bg-zinc-100 rounded"></div>
            </div>
            <div v-else-if="unpaidBookings.length === 0" class="text-center py-8 text-emerald-600">
                All paid up! Your members are on top of it 🎉
            </div>
            <div v-else class="space-y-3 max-h-[240px] overflow-y-auto">
                <div
                    v-for="booking in unpaidBookings"
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
                            class="text-sm text-sky-500 hover:text-sky-700 font-[500]"
                        >
                            {{ booking.member.name }}
                        </Link>
                        <p class="text-xs text-zinc-500">{{ booking.trainer.name }}</p>
                    </div>
                    <a
                        @click.prevent="$emit('mark-as-paid', booking.id)"
                        href="#"
                        class="px-3 py-1 bg-gray-800 :hover:bg-gray-700 text-white text-[12px] font-[500] rounded-md flex-shrink-0 inline-block"
                    >
                        Mark as Paid
                    </a>
                </div>
            </div>
        </div>

        <!-- Frozen Section -->
        <div class="pt-6 border-t border-zinc-200">
            <h3 class="text-sm font-[600] text-zinc-900 mb-4">
                Frozen
                <Badge v-if="frozenBookings.length > 0" type="error" class="ml-2">{{ frozenBookings.length }}</Badge>
            </h3>
            <div v-if="loading" class="animate-pulse space-y-3">
                <div class="h-12 bg-zinc-100 rounded"></div>
                <div class="h-12 bg-zinc-100 rounded"></div>
                <div class="h-12 bg-zinc-100 rounded"></div>
            </div>
            <div v-else-if="frozenBookings.length === 0" class="text-center py-8 text-emerald-600">
                Everyone's active and committed! 💪
            </div>
            <div v-else class="space-y-3 max-h-[240px] overflow-y-auto">
                <div
                    v-for="booking in frozenBookings"
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
                            class="text-sm text-sky-500 hover:text-sky-700 font-[500]"
                        >
                            {{ booking.member.name }}
                        </Link>
                        <p class="text-xs text-zinc-500">{{ booking.trainer.name }}</p>
                    </div>
                    <Link
                        :href="route('admin.bookings.unfreeze.index', booking.id)"
                        class="px-3 py-1 bg-gray-800 :hover:bg-gray-700 text-white text-[12px] font-[500] rounded-md flex-shrink-0 inline-block"
                    >
                        Unfreeze
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Badge from '@/Components/Layout/Badge.vue'

const { route } = window

defineProps({
    unpaidBookings: { type: Array, default: () => [] },
    frozenBookings: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
})

defineEmits(['mark-as-paid'])
</script>
