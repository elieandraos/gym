<template>
    <AppLayout title="Booking">
        <Container>
            <page-back-button>Back</page-back-button>

            <div class="flex justify-between items-center pb-6 mb-12 border-b border-zinc-200">
                <div class="grow">
                    <div class="flex gap-x-4">
                        <h1 class="text-xl font-bold text-zinc-950">Session Details</h1>
                        <Badge :type="badge_type">{{ status }}</Badge>
                    </div>
                    <div class="flex gap-x-12 mt-3 text-sm text-zinc-500">
                        <div class="flex gap-x-2">
                            <UsersIcon class="w-4 text-zinc-500"></UsersIcon>
                            <span>
                                <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.users.show', { user: booking.member.id, role: 'Member' })"> {{ booking.member.name}}</Link>
                                ·
                                <Link class="text-sky-500 hover:text-sky-700 font-medium text-sm" :href="route('admin.users.show', { user: booking.trainer.id, role: 'Trainer' })"> {{ booking.trainer.name}}</Link>
                            </span>
                        </div>
                        <div class="flex gap-x-2">
                            <ClockIcon class="w-4 text-zinc-500"></ClockIcon>
                            <span>{{ formatted_date }} · {{ start_time }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-x-4">
                    <SecondaryButton @click="showChangeDateModal = true">Change date</SecondaryButton>
                    <SecondaryButton @click="showMarkAsACancelledModal = true">Cancel Session</SecondaryButton>
                    <PrimaryButton>Add workout</PrimaryButton>
                </div>
            </div>

            Workout details to follow ...
        </Container>

        <teleport to="#modals">
            <modal v-model="showChangeDateModal">
                <h2 class="font-bold text-zinc-950 capitalize">Update session</h2>
                <p class=" text-zinc-500 text-sm">Change the session date and time.</p>
                <hr class="border-t my-2">

                <div class="my-8">
                    <div class="space-y-6">
                        <section class="flex gap-6 items-center">
                            <div class="space-y-1">
                                <h2 class="font-semibold text-zinc-950 sm:text-sm">Date</h2>
                            </div>
                            <div>
                                <DateInput v-model="date"></DateInput>
                            </div>
                        </section>
                        <section class="flex gap-6 items-center">
                            <div class="space-y-1">
                                <h2 class="font-semibold text-zinc-950 sm:text-sm">Time</h2>
                            </div>
                            <div>
                                <TimeInput v-model="start_time" />
                            </div>
                        </section>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <TransparentButton @click="showChangeDateModal = false">exit</TransparentButton>
                    <primary-button>update date and time</primary-button>
                </div>
            </modal>

            <modal v-model="showMarkAsACancelledModal">
                <h2 class="font-bold text-zinc-950 capitalize">Cancel Session</h2>
                <hr class="border-t my-2">
                <div class=" text-zinc-500 my-8">Are you sure you want to cancel this session?</div>
                <div class="flex justify-end mt-8 gap-4">
                    <TransparentButton @click="showMarkAsACancelledModal = false">exit</TransparentButton>
                    <primary-button>Yes, Cancel it</primary-button>
                </div>
            </modal>
        </teleport>
    </AppLayout>
</template>

<script setup>

import DateInput from '@/Components/Form/DateInput.vue'
import TimeInput from '@/Components/Form/TimeInput.vue'
import Badge from '@/Components/Layout/Badge.vue'
import Container from '@/Components/Layout/Container.vue'
import Modal from '@/Components/Layout/Modal.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { UsersIcon, ClockIcon }  from '@heroicons/vue/24/solid/index.js'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    bookingSlot: { type: Object, required: true }
})

const { booking, formatted_date , start_time, status, badge_type, date} = props.bookingSlot

const showChangeDateModal = ref(false)
const showMarkAsACancelledModal = ref(false)
</script>
