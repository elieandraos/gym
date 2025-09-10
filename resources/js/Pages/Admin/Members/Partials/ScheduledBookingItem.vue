<template>
    <div class="flex items-center gap-3">
        <BellIcon class="size-6 text-gray-500 flex-shrink-0" />
        <div class="flex-grow">
            <span v-if="hasScheduledBooking">
                Scheduled
                <Link :href="route('admin.bookings.show', scheduledBooking.id)" class="text-blue-600 hover:text-blue-800 font-medium">
                    training
                </Link>
                from {{ scheduledBooking.formatted_start_date }} until {{ scheduledBooking.formatted_end_date }}
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
</script>
