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

            No workout details added yet.
        </Container>

        <teleport to="#modals">
            <modal v-model="showChangeDateModal">
                <ChangeDateModal :date="date" :start_time="start_time" @close="showChangeDateModal = false" @change=""></ChangeDateModal>
            </modal>

            <modal v-model="showMarkAsACancelledModal">
                <MarkAsCancelledModal @close="showMarkAsACancelledModal = false" @cancel=""></MarkAsCancelledModal>
            </modal>
        </teleport>
    </AppLayout>
</template>

<script setup>
import Badge from '@/Components/Layout/Badge.vue'
import Container from '@/Components/Layout/Container.vue'
import Modal from '@/Components/Layout/Modal.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MarkAsCancelledModal from '@/Pages/Admin/BookingsSlots/Partials/MarkAsCancelledModal.vue'
import { UsersIcon, ClockIcon }  from '@heroicons/vue/24/solid/index.js'
import ChangeDateModal from '@/Pages/Admin/BookingsSlots/Partials/ChangeDateModal.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    bookingSlot: { type: Object, required: true }
})

const { booking, formatted_date , start_time, status, badge_type, date} = props.bookingSlot

const showChangeDateModal = ref(false)
const showMarkAsACancelledModal = ref(false)
</script>
