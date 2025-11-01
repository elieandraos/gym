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
                    <div class="text-[#71717b]">{{ age }} years old · joined on {{  since }}</div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <Link v-if="isMemberPersonalInfoPage" :href="route('admin.members.edit', id)">
                <SecondaryButton>Edit Information</SecondaryButton>
            </Link>

            <Dropdown direction="left">
                <div class="space-y-2 font-normal">
                    <template v-if="isTraining && !isBookingShowPage">
                        <Link :href="route('admin.bookings.show', active_booking.id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Training schedule</Link>
                    </template>
                    <template v-if="isTraining && !active_booking.is_frozen && !isFreezeTrainingPage">
                        <Link :href="route('admin.bookings.freeze.index', active_booking.id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Freeze training</Link>
                    </template>
                    <template v-if="!isMemberPersonalInfoPage">
                        <hr class="border-gray-200">
                        <Link :href="route('admin.members.personal-info', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Personal info</Link>
                    </template>
                    <template v-if="!isBodyCompositionCreatePage">
                        <hr v-if="isMemberPersonalInfoPage" class="border-gray-200">
                        <Link :href="route('admin.members.body-composition.create', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Body composition</Link>
                    </template>
                    <template v-if="!isMemberHistoryPage">
                        <hr class="border-gray-200">
                        <Link :href="route('admin.members.bookings.history', { user: id })" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">Trainings history</Link>
                    </template>
                    <template v-if="!isMemberShowPage">
                        <hr class="border-gray-200">
                        <Link :href="route('admin.members.show', id)" class="block p-2 hover:bg-zinc-100 hover:rounded-lg">{{ first_name }}'s profile</Link>
                    </template>
                </div>
            </Dropdown>
        </div>
    </div>
</template>

<script setup>
import Dropdown from '@/Components/Layout/Dropdown.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'

import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const $page = usePage()

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { id, name, first_name, profile_photo_url, age, since, active_booking } = props.member

const isTraining = computed(() => !!active_booking)
const isMemberShowPage = computed(() => route().current('admin.members.show'))
const isBookingShowPage = computed(() => route().current('admin.bookings.show'))
const isMemberHistoryPage = computed(() => route().current('admin.members.bookings.history'))
const isMemberPersonalInfoPage = computed(() => route().current('admin.members.personal-info'))
const isBodyCompositionCreatePage = computed(() => route().current('admin.members.body-composition.create'))
const isFreezeTrainingPage = computed(() => route().current('admin.bookings.freeze.index'))
</script>
