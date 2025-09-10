<template>
    <div class="flex items-center gap-3">
        <CalendarDaysIcon class="size-6 text-gray-500 flex-shrink-0" />
        <div class="flex-grow">
            <span v-if="isTraining">
                Currently
                <Link :href="route('admin.bookings.show', activeBooking.id)" class="text-blue-600 hover:text-blue-800 font-medium">
                    training
                </Link>
                until {{ activeBooking.formatted_end_date }}
            </span>
            <span v-else>
                Currently not training
            </span>
        </div>
    </div>
</template>

<script setup>
import { CalendarDaysIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking } = props.member

const isTraining = computed(() => !!activeBooking)
</script>
