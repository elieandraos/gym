<template>
    <div class="space-y-4">
        <!-- Scheduled Training Banner -->
        <Banner type="info">
            <div class="flex items-center gap-3">
                <CalendarDaysIcon class="size-6 flex-shrink-0" />
                <div>
                    <p v-if="isTraining && hasScheduledBooking">
                        <strong>{{ first_name }}</strong> is currently
                        <Link :href="route('admin.bookings.show', active_booking.id)" class="text-blue-900 font-semibold underline hover:no-underline">
                            training
                        </Link>
                        until {{ active_booking.formatted_end_date }} and has a
                        <Link :href="route('admin.bookings.show', scheduledBooking.id)" class="text-blue-900 font-semibold underline hover:no-underline">
                            scheduled training
                        </Link>
                        from {{ scheduledBooking.formatted_start_date }} until {{ scheduledBooking.formatted_end_date }}.
                    </p>
                    <p v-else-if="hasScheduledBooking">
                        <strong>{{ first_name }}</strong> has a
                        <Link :href="route('admin.bookings.show', scheduledBooking.id)" class="text-blue-900 font-semibold underline hover:no-underline">
                            scheduled training
                        </Link>
                        from {{ scheduledBooking.formatted_start_date }} until {{ scheduledBooking.formatted_end_date }}.
                    </p>
                    <p v-else-if="isTraining">
                        <strong>{{ first_name }}</strong> is currently
                        <Link :href="route('admin.bookings.show', active_booking.id)" class="text-blue-900 font-semibold underline hover:no-underline">
                            training
                        </Link>
                        until {{ active_booking.formatted_end_date }}.
                    </p>
                    <p v-else>
                        <strong>{{ first_name }}</strong> is not currently training.
                    </p>
                </div>
            </div>
        </Banner>

        <!-- Payment Status Banner -->
        <Banner 
            v-if="isTraining"
            :type="active_booking.is_paid ? 'success' : 'error'"
        >
            <div class="flex items-center justify-between w-full">
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
                    class="bg-red-700 hover:bg-red-800 cursor-pointer text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors ml-4"
                >
                    Mark as paid
                </button>
            </div>
        </Banner>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed, toRefs } from 'vue'
import { CalendarDaysIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import Banner from '@/Components/Banner.vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { first_name, active_booking, scheduled_bookings } = toRefs(props.member)

const isTraining = computed(() => !!active_booking.value)
const hasScheduledBooking = computed(() => scheduled_bookings.value && scheduled_bookings.value.length > 0)
const scheduledBooking = computed(() => scheduled_bookings.value?.[0])

const markAsPaid = () => {
    if (!active_booking.value) return

    router.patch(route('admin.bookings.mark-as-paid', active_booking.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            active_booking.value.is_paid = true
        }
    })
}
</script>
