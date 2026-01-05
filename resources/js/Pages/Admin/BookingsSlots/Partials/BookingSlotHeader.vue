<template>
    <div class="flex justify-between items-center font-normal gap-4">
        <PageBackButton />

        <div class="grow flex items-center gap-x-3">
            <div class="flex -space-x-3 flex-shrink-0">
                <img :src="booking.member.profile_photo_url" :alt="booking.member.name" class="size-10 rounded-full border-2 border-white relative z-10" />
                <img :src="booking.trainer.profile_photo_url" :alt="booking.trainer.name" class="size-10 rounded-full border-2 border-white" />
            </div>
            <div>
                <div class="text-[15px]">
                    <Link class="text-sky-500 hover:text-sky-700 font-[400]" :href="route('admin.members.show', { user: booking.member.id })">{{ booking.member.name}}</Link>
                    ·
                    <Link class="text-sky-500 hover:text-sky-700 font-[400]" :href="route('admin.trainers.show', { user: booking.trainer.id })">{{ booking.trainer.name}}</Link>
                </div>
                <div class="flex gap-x-4 items-center mt-0.5 text-[#71717b]">
                    <div class="flex gap-x-2 items-center">
                        <ClockIcon class="w-4 text-[#71717b] flex-shrink-0"></ClockIcon>
                        <span class="text-[15px]">{{ formatted_date }} · {{ start_time }}</span>
                    </div>
                    <Badge class="text-xs" :type="badge_type">{{ status }}</Badge>
                </div>
            </div>
        </div>

        <div v-if="withMenu" class="flex items-center gap-2">
            <Link v-if="bookingId" :href="route('admin.bookings.show', bookingId)" class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer" title="View booking schedule">
                <CalendarDaysIcon class="w-5 h-5 text-zinc-600" />
            </Link>
            <button @click="showPreviousSessionsModal = true" class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer" title="View previous sessions">
                <ClockIcon class="w-5 h-5 text-zinc-600" />
            </button>
            <dropdown direction="left">
                <div class="space-y-2">
                    <Link :href="route('admin.change-booking-slot-date-time.edit', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Change date & time</Link>
                    <hr class="border-gray-200">
                    <Link :href="route('admin.members.show', { user: booking.member.id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">{{ booking.member.name.split(' ')[0] }}'s profile</Link>
                    <hr class="border-gray-200">
                    <a :href="route('admin.bookings-slots.cancel.index', id)" class="block p-2 text-red-500 hover:bg-red-50 hover:rounded-lg">Cancel session</a>
                </div>
            </dropdown>
        </div>

        <!-- Previous Sessions Modal -->
        <PreviousSessionsModal
            :show="showPreviousSessionsModal"
            :booking-slot-id="id"
            @close="showPreviousSessionsModal = false"
        />
    </div>
</template>

<script setup>
import { ref, toRefs } from 'vue'
import { Link } from '@inertiajs/vue3'
import Badge from '@/Components/Layout/Badge.vue'
import Dropdown from '@/Components/Layout/Dropdown.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PreviousSessionsModal from './PreviousSessionsModal.vue'
import { CalendarDaysIcon } from '@heroicons/vue/24/solid'
import { ClockIcon } from '@heroicons/vue/24/outline'

const { route } = window

const props = defineProps({
    bookingSlot: { type: Object, required: true },
    bookingId: { type: [Number, String], default: null },
    withMenu: { type: Boolean, default: false },
})

const {
    id,
    booking,
    formatted_date,
    start_time,
    status,
    badge_type,
} = toRefs(props.bookingSlot)

const showPreviousSessionsModal = ref(false)
</script>
