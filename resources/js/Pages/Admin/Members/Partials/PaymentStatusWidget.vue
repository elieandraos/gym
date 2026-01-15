<template>
    <div class="bg-white rounded-lg text-[#71717b]">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0">
                <CurrencyDollarIcon class="size-12 text-zinc-200" />
            </div>
            <div class="flex-1">
                <div v-if="isTraining">
                    <div class="flex items-center gap-2">
                        <span>Current training is</span>
                        <Badge :type="activeBooking.is_paid ? 'success' : 'error'">
                            {{ activeBooking.is_paid ? 'paid' : 'not paid' }}
                        </Badge>
                    </div>
                    <div v-if="!activeBooking.is_paid" class="flex items-center gap-2">
                        <span>{{ activeBooking.amount }} USD</span>
                        <a
                            @click="markAsPaid"
                            href="#"
                            class="text-sky-500 hover:text-sky-700 font-[400]"
                        >
                            Mark as paid
                        </a>
                    </div>
                    <div v-else>
                        <span>{{ activeBooking.amount }} USD</span>
                    </div>
                </div>
                <div v-else>
                    <p class="text-gray-500">No active training</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import Badge from '@/Components/Layout/Badge.vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking } = props.member
const isTraining = computed(() => !!activeBooking)

const markAsPaid = () => {
    if (!activeBooking) return

    router.patch(route('admin.bookings.mark-as-paid', activeBooking.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            activeBooking.is_paid = true
        }
    })
}
</script>
