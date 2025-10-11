<template>
    <div class="bg-zinc-50 rounded-lg p-6">
        <h3 class="text-sm font-[500] text-zinc-900 mb-4">
            Frozen
            <Badge v-if="bookings.length > 0" class="ml-2">{{ bookings.length }}</Badge>
        </h3>
        <div v-if="loading" class="animate-pulse space-y-3">
            <div class="h-12 bg-zinc-100 rounded"></div>
            <div class="h-12 bg-zinc-100 rounded"></div>
            <div class="h-12 bg-zinc-100 rounded"></div>
        </div>
        <div v-else-if="bookings.length === 0" class="text-center py-8 text-zinc-400">
            No frozen bookings
        </div>
        <div v-else class="space-y-3 max-h-[240px] overflow-y-auto">
            <div
                v-for="booking in bookings"
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Badge from '@/Components/Layout/Badge.vue'

const { route } = window

defineProps({
    bookings: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
})
</script>
