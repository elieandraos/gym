<template>
    <div class="flex justify-between items-end">
        <div class="flex flex-wrap grow items-center gap-4">
            <PageBackButton />
            <div class="flex gap-2 items-center">
                <img class="size-12 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-[20px] font-[600] leading-[32px]">{{ name }}</h1>
                    </div>
                    <div class="text-[#71717b]">{{ age }} years old · Member since {{  since }}</div>
                </div>
            </div>
        </div>

        <Dropdown direction="right">
            <div class="space-y-2 font-normal">
                <Link v-if="isTraining && !isBookingShowPage" :href="route('admin.bookings.show', active_booking.id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Training schedule</Link>
                <Link v-if="!isMemberHistoryPage" :href="route('admin.members.bookings.history', { user: id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Trainings history</Link>
                <Link v-if="!isMemberPersonalInfoPage" :href="route('admin.members.personal-info', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Personal info</Link>
                <template v-if="!isMemberShowPage">
                    <hr class="border-gray-200">
                    <Link :href="route('admin.members.show', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">{{ first_name }}'s profile</Link>
                </template>
            </div>
        </Dropdown>
    </div>
</template>

<script setup>
import Dropdown from '@/Components/Layout/Dropdown.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'

import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const $page = usePage()

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { id, name, first_name, profile_photo_url, age, since, active_booking } = props.member

const isTraining = computed(() => !!active_booking)
const isMemberShowPage = computed(() => $page.url === '/members/' + id)
const isBookingShowPage = computed(() => active_booking && $page.url.includes('/bookings/' + active_booking.id))
const isMemberHistoryPage = computed(() => $page.url.includes('/members/' + id + '/bookings/history'))
const isMemberPersonalInfoPage = computed(() => $page.url.includes('/members/' + id + '/personal-info'))
</script>
