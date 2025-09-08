<template>
    <div class="space-y-4">
        <!-- Scheduled Training Banner -->
        <div class="w-full bg-indigo-50 text-base p-4 rounded-lg border border-indigo-100 text-indigo-800 flex items-center gap-3">
            <CalendarDaysIcon class="size-6 flex-shrink-0" />
            <div>
                <p v-if="isTraining && hasScheduledBooking">
                    <strong>{{ first_name }}</strong> is currently
                    <Link :href="route('admin.bookings.show', active_booking.id)" class="text-indigo-900 font-semibold underline hover:no-underline">
                        training
                    </Link>
                    until {{ active_booking.formatted_end_date }} and has a
                    <Link :href="route('admin.bookings.show', scheduledBooking.id)" class="text-indigo-900 font-semibold underline hover:no-underline">
                        scheduled training
                    </Link>
                    from {{ scheduledBooking.formatted_start_date }} until {{ scheduledBooking.formatted_end_date }}.
                </p>
                <p v-else-if="hasScheduledBooking">
                    <strong>{{ first_name }}</strong> has a
                    <Link :href="route('admin.bookings.show', scheduledBooking.id)" class="text-indigo-900 font-semibold underline hover:no-underline">
                        scheduled training
                    </Link>
                    from {{ scheduledBooking.formatted_start_date }} until {{ scheduledBooking.formatted_end_date }}.
                </p>
                <p v-else-if="isTraining">
                    <strong>{{ first_name }}</strong> is currently
                    <Link :href="route('admin.bookings.show', active_booking.id)" class="text-indigo-900 font-semibold underline hover:no-underline">
                        training
                    </Link>
                    until {{ active_booking.formatted_end_date }}.
                </p>
                <p v-else>
                    <strong>{{ first_name }}</strong> is not currently training.
                </p>
            </div>
        </div>

        <!-- Payment Status Banner -->
        <div
            v-if="isTraining"
            :class="[
                'w-full text-base p-4 rounded-lg border flex items-center justify-between',
                active_booking.is_paid
                    ? 'bg-lime-50 border-lime-100 text-lime-800'
                    : 'bg-red-50 border-red-100 text-red-800'
            ]"
        >
            <div class="flex items-center gap-3">
                <CurrencyDollarIcon class="size-6 flex-shrink-0" />
                <p>
                    <strong>{{ first_name }}</strong>'s current training is
                    <span class="font-semibold">
                        {{ active_booking.is_paid ? 'paid' : 'not paid' }}
                    </span>.
                </p>
            </div>

            <button
                v-if="!active_booking.is_paid"
                @click="markAsPaid"
                class="bg-red-700 hover:bg-red-800 cursor-pointer text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
                Mark as paid
            </button>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { CalendarDaysIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { first_name, active_booking, scheduled_bookings } = props.member

const isTraining = computed(() => !!active_booking)
const hasScheduledBooking = computed(() => scheduled_bookings && scheduled_bookings.length > 0)
const scheduledBooking = computed(() => scheduled_bookings?.[0])

const markAsPaid = () => {
    if (!active_booking) return

    router.patch(route('admin.bookings.mark-as-paid', active_booking.id), {}, {
        onSuccess: () => {
            // Inertia will automatically reload the page with fresh data
        }
    })
}
</script>
