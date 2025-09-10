<template>
    <div v-if="isTraining" class="flex items-center gap-3">
        <CurrencyDollarIcon class="size-6 text-gray-500 flex-shrink-0" />
        <div class="flex-grow">
            <span class="flex items-center gap-2">
                Current training is
                <Badge :type="activeBooking.is_paid ? 'success' : 'error'">
                    {{ activeBooking.is_paid ? 'paid' : 'not paid' }}
                </Badge>
            </span>
        </div>
        <button
            v-if="!activeBooking.is_paid"
            @click="markAsPaid"
            class="text-blue-600 hover:text-blue-800 font-medium text-sm"
        >
            Mark as paid
        </button>
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
