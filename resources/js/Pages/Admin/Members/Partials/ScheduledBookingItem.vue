<template>
    <div class="flex items-center gap-3">
        <BellIcon class="size-6 text-[#71717b] flex-shrink-0" />
        <div class="flex-grow">
            <span v-if="hasScheduledBooking">
                <Link :href="route('admin.bookings.show', scheduledBooking.id)" class="text-sky-500 hover:text-sky-700 font-[500]">
                    Scheduled training
                </Link>
                from <span class="font-[600] text-indigo-950">{{ formatted_start_date }}</span>
                until <span class="font-[600] text-indigo-950">{{ formatted_end_date }}</span>
            </span>
            <span v-else>
                No scheduled training
            </span>
        </div>
    </div>
</template>

<script setup>
import { BellIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { scheduled_bookings: scheduledBookings } = props.member
const hasScheduledBooking = computed(() => scheduledBookings && scheduledBookings.length > 0)
const scheduledBooking = computed(() => scheduledBookings?.[0])
const {formatted_start_date, formatted_end_date} = scheduledBooking.value || {}
</script>
